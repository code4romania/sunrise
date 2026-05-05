<?php

declare(strict_types=1);

use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\MonitoringResource;
use App\Models\Beneficiary;
use App\Models\InterventionPlan;
use App\Models\Monitoring;
use App\Models\MonitoringChild;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->withOrganization()->create();
    $this->organization = $this->user->organizations->first();
    $this->actingAs($this->user);
    Filament::setTenant($this->organization);
    Filament::bootCurrentPanel();
});

it('shows per-tab edit buttons on monitoring view', function (): void {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create();
    InterventionPlan::factory()
        ->for($beneficiary)
        ->for($this->organization)
        ->create();

    $monitoring = Monitoring::query()->forceCreate([
        'beneficiary_id' => $beneficiary->id,
        'date' => now()->toDateString(),
        'number' => 101,
        'start_date' => now()->subDays(30)->toDateString(),
        'end_date' => now()->toDateString(),
        'admittance_date' => now()->subDays(60)->toDateString(),
        'admittance_disposition' => 'Dispozitie test',
        'services_in_center' => 'Servicii test',
        'protection_measures' => ['objection' => 'O', 'activity' => 'A', 'conclusion' => 'C'],
        'health_measures' => ['objection' => 'O', 'activity' => 'A', 'conclusion' => 'C'],
        'legal_measures' => ['objection' => 'O', 'activity' => 'A', 'conclusion' => 'C'],
        'psychological_measures' => ['objection' => 'O', 'activity' => 'A', 'conclusion' => 'C'],
        'aggressor_relationship' => ['objection' => 'O', 'activity' => 'A', 'conclusion' => 'C'],
        'others' => ['objection' => 'O', 'activity' => 'A', 'conclusion' => 'C'],
        'progress' => 'Progres test',
        'observation' => 'Observatie test',
    ]);

    MonitoringChild::factory()
        ->for($monitoring)
        ->create([
            'name' => 'Copil test',
            'status' => 'safe',
            'age' => 10,
        ]);

    $url = MonitoringResource::getUrl('view', [
        'beneficiary' => $beneficiary,
        'record' => $monitoring,
        'tenant' => $this->organization,
    ]);

    $this->get($url)
        ->assertSuccessful()
        ->assertSee(__('intervention_plan.actions.create_monthly_plan_with_plans'), escape: false)
        ->assertSee(__('monitoring.titles.edit_details'), escape: false)
        ->assertSee(__('monitoring.titles.edit_children'), escape: false)
        ->assertSee(__('monitoring.titles.edit_general'), escape: false);
});
