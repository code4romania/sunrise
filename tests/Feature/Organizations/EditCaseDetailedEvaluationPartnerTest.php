<?php

declare(strict_types=1);

use App\Enums\AddressType;
use App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation\EditCaseDetailedEvaluationPartner;
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

it('persists partner legal and effective residence addresses when saving the partner section', function () {
    $county = County::query()->first();
    $city = City::query()->where('county_id', $county->id)->first();

    if (! $county || ! $city) {
        $this->markTestSkipped('Counties and cities must be seeded for this test.');
    }

    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create(['first_name' => 'Test', 'last_name' => 'Beneficiary']);

    $partner = $beneficiary->partner;
    expect($partner)->not->toBeNull();

    $legalAddress = str_repeat('L', 75);
    $effectiveAddress = str_repeat('E', 75);

    livewire(EditCaseDetailedEvaluationPartner::class, [
        'record' => $beneficiary->getKey(),
        'tenant' => $this->organization,
    ])
        ->fillForm([
            'partner' => [
                'last_name' => 'PartnerLN',
                'first_name' => 'PartnerFN',
                'same_as_legal_residence' => false,
                'legal_residence' => [
                    'county_id' => $county->id,
                    'city_id' => $city->id,
                    'address' => $legalAddress,
                ],
                'effective_residence' => [
                    'county_id' => $county->id,
                    'city_id' => $city->id,
                    'address' => $effectiveAddress,
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $partner->refresh();
    $partner->load(['legal_residence', 'effective_residence']);

    expect($partner->last_name)->toBe('PartnerLN');
    expect($partner->legal_residence)->not->toBeNull();
    expect($partner->legal_residence->address)->toBe($legalAddress);
    expect($partner->legal_residence->address_type)->toBe(AddressType::LEGAL_RESIDENCE);

    expect($partner->effective_residence)->not->toBeNull();
    expect($partner->effective_residence->address)->toBe($effectiveAddress);
    expect($partner->effective_residence->address_type)->toBe(AddressType::EFFECTIVE_RESIDENCE);
});
