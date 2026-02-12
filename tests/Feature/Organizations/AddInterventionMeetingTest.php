<?php

declare(strict_types=1);

use App\Enums\GeneralStatus;
use App\Enums\MeetingStatus;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\ViewCaseInterventionService;
use App\Models\Beneficiary;
use App\Models\BeneficiaryIntervention;
use App\Models\InterventionMeeting;
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

it('can add a ședință/activitate from intervention service page', function () {
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

    $meetingDate = now()->format('Y-m-d');

    livewire(ViewCaseInterventionService::class, [
        'record' => $beneficiary->getKey(),
        'interventionService' => $interventionService->getKey(),
        'tenant' => $organization,
    ])
        ->callAction('add_meeting', data: [
            'organization_service_intervention_id' => $organizationServiceIntervention->id,
            'specialist_id' => $specialist->id,
            'status' => MeetingStatus::PLANED,
            'date' => $meetingDate,
            'time' => '10:00',
            'duration' => 60,
            'topic' => 'Test topic',
            'observations' => 'Test observations',
        ])
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(BeneficiaryIntervention::class, [
        'intervention_service_id' => $interventionService->id,
        'organization_service_intervention_id' => $organizationServiceIntervention->id,
        'specialist_id' => $specialist->id,
    ]);

    $beneficiaryIntervention = BeneficiaryIntervention::query()
        ->where('intervention_service_id', $interventionService->id)
        ->where('organization_service_intervention_id', $organizationServiceIntervention->id)
        ->first();
    $this->assertNotNull($beneficiaryIntervention);
    $this->assertDatabaseHas(InterventionMeeting::class, [
        'beneficiary_intervention_id' => $beneficiaryIntervention->id,
        'specialist_id' => $specialist->id,
        'status' => MeetingStatus::PLANED->value,
        'topic' => 'Test topic',
        'observations' => 'Test observations',
    ]);
});
