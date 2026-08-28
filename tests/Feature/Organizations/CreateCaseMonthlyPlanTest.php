<?php

declare(strict_types=1);

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\CreateCaseMonthlyPlan;
use App\Models\Beneficiary;
use App\Models\InterventionPlan;
use App\Models\MonthlyPlan;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
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

it('renders create monthly plan page when intervention plan exists', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create();

    InterventionPlan::factory()
        ->for($beneficiary)
        ->for($this->organization)
        ->create();

    $url = CaseResource::getUrl('create_monthly_plan', [
        'case' => $beneficiary,
        'tenant' => $this->organization,
    ]);

    $this->get($url)
        ->assertSuccessful()
        ->assertSee(__('intervention_plan.wizard.monthly_plan_general'), escape: false)
        ->assertSee(__('intervention_plan.headings.services_and_interventions'), escape: false);
});

it('redirects to intervention plan view when beneficiary has no intervention plan', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create();

    $url = CaseResource::getUrl('create_monthly_plan', [
        'case' => $beneficiary,
        'tenant' => $this->organization,
    ]);

    $this->get($url)
        ->assertRedirect(CaseResource::getUrl('view_intervention_plan', [
            'record' => $beneficiary,
            'tenant' => $this->organization,
        ]));
});

it('advances wizard through both steps and creates a monthly plan', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create();

    InterventionPlan::factory()
        ->for($beneficiary)
        ->for($this->organization)
        ->create();

    $service = Service::factory()->create(['status' => true]);
    $service->serviceInterventions()->update(['status' => true]);

    expect(MonthlyPlan::query()->where('intervention_plan_id', $beneficiary->interventionPlan->id)->count())->toBe(0);

    livewire(CreateCaseMonthlyPlan::class, [
        'case' => (string) $beneficiary->getKey(),
        'tenant' => $this->organization,
    ])
        ->assertWizardCurrentStep(1)
        ->fillForm([
            'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
            'case_manager_user_id' => $this->user->id,
            'specialists' => [],
        ])
        ->goToNextWizardStep()
        ->assertHasNoFormErrors()
        ->assertWizardCurrentStep(2)
        ->fillForm([
            'monthlyPlanServices' => [
                [
                    'service_id' => $service->id,
                    'monthlyPlanInterventions' => [],
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $monthlyPlan = MonthlyPlan::query()
        ->where('intervention_plan_id', $beneficiary->fresh()->interventionPlan->id)
        ->first();

    expect($monthlyPlan)->not->toBeNull()
        ->and($monthlyPlan->case_manager_user_id)->toBe($this->user->id);
});
