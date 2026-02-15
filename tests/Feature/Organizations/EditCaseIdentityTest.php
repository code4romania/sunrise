<?php

declare(strict_types=1);

use App\Enums\AddressType;
use App\Enums\ResidenceEnvironment;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseIdentity;
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

it('can render edit case identity page', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create(['first_name' => 'Test', 'last_name' => 'Beneficiary']);

    $url = CaseResource::getUrl('edit_identity', [
        'record' => $beneficiary,
        'tenant' => $this->organization,
    ]);

    $this->get($url)
        ->assertSuccessful();
});

it('saves legal and effective residence addresses when editing identity', function () {
    $county = County::query()->first();
    $city = City::query()->where('county_id', $county->id)->first();

    if (! $county || ! $city) {
        $this->markTestSkipped('Counties and cities must be seeded for this test.');
    }

    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create(['first_name' => 'Test', 'last_name' => 'Beneficiary']);

    expect($beneficiary->legal_residence)->toBeNull();
    expect($beneficiary->effective_residence)->toBeNull();

    livewire(EditCaseIdentity::class, [
        'record' => $beneficiary->getKey(),
        'tenant' => $this->organization,
    ])
        ->fillForm([
            'legal_residence' => [
                'county_id' => $county->id,
                'city_id' => $city->id,
                'address' => 'Str. Test 1',
                'environment' => ResidenceEnvironment::URBAN,
            ],
            'same_as_legal_residence' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $beneficiary->refresh();
    $beneficiary->load(['legal_residence', 'effective_residence']);

    expect($beneficiary->legal_residence)->not->toBeNull();
    expect($beneficiary->legal_residence->county_id)->toBe($county->id);
    expect($beneficiary->legal_residence->city_id)->toBe($city->id);
    expect($beneficiary->legal_residence->address)->toBe('Str. Test 1');
    expect($beneficiary->legal_residence->address_type)->toBe(AddressType::LEGAL_RESIDENCE);

    expect($beneficiary->effective_residence)->not->toBeNull();
    expect($beneficiary->effective_residence->address_type)->toBe(AddressType::EFFECTIVE_RESIDENCE);
});
