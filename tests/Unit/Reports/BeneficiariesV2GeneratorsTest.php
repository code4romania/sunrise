<?php

declare(strict_types=1);

use App\Enums\CaseStatus;
use App\Enums\Ternary;
use App\Models\Beneficiary;
use App\Models\User;
use App\Services\Reports\BeneficiariesReports\CasesByAgeSegmentation;
use App\Services\Reports\BeneficiariesReports\CasesByEvaluationInitialRiskFactors;
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

it('segments ages with 17 as minor and 18 as major', function (): void {
    Beneficiary::factory()
        ->for($this->organization)
        ->create([
            'status' => CaseStatus::ACTIVE,
            'birthdate' => now()->subYears(17)->format('Y-m-d'),
        ]);

    Beneficiary::factory()
        ->for($this->organization)
        ->create([
            'status' => CaseStatus::ACTIVE,
            'birthdate' => now()->subYears(18)->format('Y-m-d'),
        ]);

    $reportData = (new CasesByAgeSegmentation)
        ->setEndDate(now()->toDateString())
        ->setShowMissingValues(true)
        ->setAddCasesInMonitoring(true)
        ->getReportData();

    $minorCases = (int) ($reportData->firstWhere('age_group', 'minor')->total_cases ?? 0);
    $majorCases = (int) ($reportData->firstWhere('age_group', 'major')->total_cases ?? 0);

    expect($minorCases)->toBeGreaterThanOrEqual(1);
    expect($majorCases)->toBeGreaterThanOrEqual(1);
});

it('counts initial evaluation risk factor answers marked as yes', function (): void {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create([
            'status' => CaseStatus::ACTIVE,
            'birthdate' => now()->subYears(30)->format('Y-m-d'),
        ]);

    $beneficiary->riskFactors()->update([
        'risk_factors' => [
            'aggressor_present_risk_related_to_vices' => ['value' => Ternary::YES->value],
            'aggressor_is_possessive_or_jealous' => ['value' => Ternary::NO->value],
            'aggressor_have_mental_problems' => ['value' => Ternary::NO->value],
            'aggressor_present_manifestations_of_economic_stress' => ['value' => Ternary::NO->value],
            'victim_afraid_for_himself' => ['value' => Ternary::NO->value],
            'victim_has_an_attitude_of_acceptance' => ['value' => Ternary::NO->value],
            'separation' => ['value' => Ternary::NO->value],
            'aggressor_parent_has_contact_with_children' => ['value' => Ternary::NO->value],
            'aggressor_parent_threaten_the_victim_in_the_visitation_program' => ['value' => Ternary::NO->value],
            'children_from_other_marriage_are_integrated_into_family' => ['value' => Ternary::NO->value],
            'domestic_violence_during_pregnancy' => ['value' => Ternary::NO->value],
        ],
    ]);

    $reportData = (new CasesByEvaluationInitialRiskFactors)
        ->setEndDate(now()->toDateString())
        ->setShowMissingValues(true)
        ->setAddCasesInMonitoring(true)
        ->getReportData();

    $factorCases = (int) ($reportData->firstWhere('factor_key', 'aggressor_present_risk_related_to_vices')->total_cases ?? 0);

    expect($factorCases)->toBe(1);
});
