<?php

declare(strict_types=1);

use App\Enums\GeneralStatus;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\ViewCaseBeneficiaryIntervention;
use App\Models\Beneficiary;
use App\Models\BeneficiaryIntervention;
use App\Models\InterventionPlan;
use App\Models\InterventionService;
use App\Models\OrganizationService;
use App\Models\OrganizationServiceIntervention;
use App\Models\Service;
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

it('can edit intervention indicators from beneficiary intervention detail page', function () {
    $organization = $this->organization;
    $service = Service::factory()->create();
    $organizationService = OrganizationService::factory()
        ->for($organization)
        ->for($service)
        ->create();
    $serviceIntervention = $service->serviceInterventions()->first();
    $organizationServiceIntervention = OrganizationServiceIntervention::factory()
        ->for($organizationService)
        ->for($serviceIntervention)
        ->for($organization)
        ->create(['status' => GeneralStatus::ACTIVE]);

    $beneficiary = Beneficiary::factory()->for($organization)->create();
    $specialist = $beneficiary->specialistsTeam()->first();
    if (! $specialist) {
        $this->markTestSkipped('Beneficiary has no specialists team');
    }

    $plan = InterventionPlan::factory()
        ->for($beneficiary)
        ->for($organization)
        ->create();
    $interventionService = InterventionService::factory()
        ->for($plan)
        ->create([
            'organization_service_id' => $organizationService->id,
            'specialist_id' => $specialist->id,
        ]);

    $beneficiaryIntervention = BeneficiaryIntervention::factory()
        ->for($interventionService)
        ->for($organizationServiceIntervention)
        ->create([
            'specialist_id' => $specialist->id,
            'objections' => 'Old objectives',
            'expected_results' => 'Old results',
            'procedure' => 'Old procedure',
            'indicators' => 'Old indicators',
            'achievement_degree' => 'Old degree',
        ]);

    livewire(ViewCaseBeneficiaryIntervention::class, [
        'record' => $beneficiary->getKey(),
        'interventionService' => $interventionService->getKey(),
        'beneficiaryIntervention' => $beneficiaryIntervention->getKey(),
        'tenant' => $organization,
    ])
        ->callAction('edit_indicators', data: [
            'objections' => 'Updated objectives',
            'expected_results' => 'Updated results',
            'procedure' => 'Updated procedure',
            'indicators' => 'Updated indicators',
            'achievement_degree' => 'Updated degree',
        ])
        ->assertHasNoFormErrors();

    $beneficiaryIntervention->refresh();
    expect($beneficiaryIntervention->objections)->toBe('Updated objectives')
        ->and($beneficiaryIntervention->expected_results)->toBe('Updated results')
        ->and($beneficiaryIntervention->procedure)->toBe('Updated procedure')
        ->and($beneficiaryIntervention->indicators)->toBe('Updated indicators')
        ->and($beneficiaryIntervention->achievement_degree)->toBe('Updated degree');
});
