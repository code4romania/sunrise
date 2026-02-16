<?php

declare(strict_types=1);

use App\Enums\AddressType;
use App\Enums\Applicant;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation\CreateCaseDetailedEvaluation;
use App\Models\Beneficiary;
use App\Models\City;
use App\Models\County;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withOrganization()->create();
    $this->organization = $this->user->organizations->first();
    $this->actingAs($this->user);
    Filament::setTenant($this->organization);
    Filament::bootCurrentPanel();
});

it('can render create detailed evaluation page', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create();

    $url = CaseResource::getUrl('edit_detailed_evaluation', [
        'record' => $beneficiary,
        'tenant' => $this->organization,
    ]);

    $this->get($url)
        ->assertSuccessful();
});

it('saves partner with legal and effective residence addresses', function () {
    $county = County::query()->first();
    $city = City::query()->where('county_id', $county->id)->first();

    if (! $county || ! $city) {
        $this->markTestSkipped('Counties and cities must be seeded for this test.');
    }

    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create();

    expect($beneficiary->partner)->toBeNull();

    livewire(CreateCaseDetailedEvaluation::class, [
        'record' => $beneficiary->getKey(),
        'tenant' => $this->organization,
    ])
        ->fillForm([
            'partner' => [
                'last_name' => 'Partner',
                'first_name' => 'Test',
                'age' => 35,
                'legal_residence' => [
                    'county_id' => $county->id,
                    'city_id' => $city->id,
                    'address' => 'Str. Partener 1',
                ],
                'same_as_legal_residence' => true,
                'observations' => 'Test observations',
            ],
            'multidisciplinaryEvaluation' => [
                'applicant' => Applicant::BENEFICIARY,
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $beneficiary->refresh();
    $beneficiary->load(['partner.legal_residence', 'partner.effective_residence']);

    expect($beneficiary->partner)->not->toBeNull();
    expect($beneficiary->partner->last_name)->toBe('Partner');
    expect($beneficiary->partner->first_name)->toBe('Test');
    expect($beneficiary->partner->legal_residence)->not->toBeNull();
    expect($beneficiary->partner->legal_residence->county_id)->toBe($county->id);
    expect($beneficiary->partner->legal_residence->city_id)->toBe($city->id);
    expect($beneficiary->partner->legal_residence->address)->toBe('Str. Partener 1');
    expect($beneficiary->partner->legal_residence->address_type)->toBe(AddressType::LEGAL_RESIDENCE->value);

    expect($beneficiary->partner->effective_residence)->not->toBeNull();
    expect($beneficiary->partner->effective_residence->address_type)->toBe(AddressType::EFFECTIVE_RESIDENCE->value);
});
