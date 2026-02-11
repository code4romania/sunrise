<?php

declare(strict_types=1);

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\EditCaseMonthlyPlanDetails;
use App\Models\Beneficiary;
use App\Models\InterventionPlan;
use App\Models\MonthlyPlan;
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

it('can render edit monthly plan details page', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create();

    $interventionPlan = InterventionPlan::factory()
        ->for($beneficiary)
        ->for($this->organization)
        ->create();

    $monthlyPlan = MonthlyPlan::create([
        'intervention_plan_id' => $interventionPlan->id,
        'start_date' => Carbon::now()->startOfMonth(),
        'end_date' => Carbon::now()->endOfMonth(),
        'case_manager_user_id' => $this->user->id,
        'specialists' => [],
    ]);

    $url = CaseResource::getUrl('edit_monthly_plan_details', [
        'record' => $beneficiary,
        'monthlyPlan' => $monthlyPlan,
        'tenant' => $this->organization,
    ]);

    $this->get($url)
        ->assertSuccessful();
});

it('can see edit monthly plan form and save', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create();

    $interventionPlan = InterventionPlan::factory()
        ->for($beneficiary)
        ->for($this->organization)
        ->create();

    $monthlyPlan = MonthlyPlan::create([
        'intervention_plan_id' => $interventionPlan->id,
        'start_date' => Carbon::now()->startOfMonth(),
        'end_date' => Carbon::now()->endOfMonth(),
        'case_manager_user_id' => $this->user->id,
        'specialists' => [],
    ]);

    livewire(EditCaseMonthlyPlanDetails::class, [
        'record' => $beneficiary->getKey(),
        'monthlyPlan' => $monthlyPlan->getKey(),
        'tenant' => $this->organization,
    ])
        ->fillForm([
            'start_date' => Carbon::now()->startOfMonth()->addMonth()->format('Y-m-d'),
            'end_date' => Carbon::now()->endOfMonth()->addMonth()->format('Y-m-d'),
            'case_manager_user_id' => $this->user->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $monthlyPlan->refresh();
    expect($monthlyPlan->start_date->format('Y-m-d'))->toBe(Carbon::now()->startOfMonth()->addMonth()->format('Y-m-d'));
});
