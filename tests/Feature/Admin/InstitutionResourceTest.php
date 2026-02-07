<?php

declare(strict_types=1);

use App\Enums\AreaType;
use App\Enums\InstitutionStatus;
use App\Enums\OrganizationType;
use App\Filament\Admin\Resources\Institutions\InstitutionResource;
use App\Filament\Admin\Resources\Institutions\Pages\CreateInstitution;
use App\Filament\Admin\Resources\Institutions\Pages\ListInstitutions;
use App\Models\City;
use App\Models\County;
use App\Models\Institution;
use App\Models\User;
use Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('can render list page', function () {
    $this->get(InstitutionResource::getUrl('index'))
        ->assertSuccessful();
});

it('can list institutions', function () {
    $institutions = Institution::factory()->count(3)->create();

    livewire(ListInstitutions::class)
        ->assertCanSeeTableRecords($institutions);
});

it('can render create page', function () {
    $this->get(InstitutionResource::getUrl('create'))
        ->assertSuccessful();
});

it('can create institution', function () {
    $city = City::query()->first();
    $county = County::find($city->county_id);

    livewire(CreateInstitution::class)
        ->fillForm([
            'name' => 'Test Institution',
            'short_name' => 'TI',
            'type' => OrganizationType::NGO->value,
            'cif' => '12345678',
            'main_activity' => 'Social services',
            'area' => AreaType::COUNTY->value,
            'county_id' => $county->id,
            'city_id' => $city->id,
            'address' => 'Test Address 1',
            'representative_person' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '0712345678',
            ],
            'contact_person' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '0712345679',
            ],
            'status' => InstitutionStatus::PENDING->value,
            'organizations' => [
                [
                    'name' => 'Test Center',
                    'short_name' => 'TC',
                    'main_activity' => 'Social assistance',
                ],
            ],
            'admins' => [
                [
                    'first_name' => 'Admin',
                    'last_name' => 'User',
                    'email' => 'admin@test-institution.example',
                    'phone_number' => '0712345680',
                    'ngo_admin' => 1,
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Institution::class, [
        'name' => 'Test Institution',
        'cif' => '12345678',
    ]);
});

it('can validate required fields on create', function () {
    livewire(CreateInstitution::class)
        ->fillForm([
            'name' => null,
            'type' => null,
            'cif' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'type' => 'required',
            'cif' => 'required',
        ]);
});
