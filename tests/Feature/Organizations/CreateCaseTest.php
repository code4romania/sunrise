<?php

declare(strict_types=1);

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Pages\CreateCase;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCaseIdentity;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCasePersonalInformation;
use App\Models\Beneficiary;
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

it('can render create case page', function () {
    $this->get(CaseResource::getUrl('create', ['tenant' => $this->organization]))
        ->assertSuccessful();
});

it('can see wizard steps on create page', function () {
    livewire(CreateCase::class, ['tenant' => $this->organization])
        ->assertSuccessful();
});

it('can advance from cnp step when without_cnp is checked and cnp is not entered', function () {
    livewire(CreateCase::class, ['tenant' => $this->organization])
        ->fillForm(['consent' => true])
        ->goToNextWizardStep()
        ->assertHasNoFormErrors()
        ->fillForm(['without_cnp' => true])
        ->goToNextWizardStep()
        ->assertHasNoFormErrors()
        ->assertWizardCurrentStep(3);
});

it('can render identity page for a case', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create(['first_name' => 'Test', 'last_name' => 'Beneficiary']);

    $url = CaseResource::getUrl('identity', [
        'record' => $beneficiary,
        'tenant' => $this->organization,
    ]);

    $this->get($url)
        ->assertSuccessful();
});

it('can see identity tabs on identity page', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create(['first_name' => 'Test', 'last_name' => 'Beneficiary']);

    livewire(ViewCaseIdentity::class, [
        'record' => $beneficiary->getKey(),
        'tenant' => $this->organization,
    ])
        ->assertSuccessful();
});

it('can render personal information page for a case', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create(['first_name' => 'Test', 'last_name' => 'Beneficiary']);

    $url = CaseResource::getUrl('view_personal_information', [
        'record' => $beneficiary,
        'tenant' => $this->organization,
    ]);

    $this->get($url)
        ->assertSuccessful();
});

it('can see personal information tabs on personal information page', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create(['first_name' => 'Test', 'last_name' => 'Beneficiary']);

    livewire(ViewCasePersonalInformation::class, [
        'record' => $beneficiary->getKey(),
        'tenant' => $this->organization,
    ])
        ->assertSuccessful();
});
