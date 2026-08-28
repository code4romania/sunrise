<?php

declare(strict_types=1);

use App\Models\Aggressor;
use App\Models\BeneficiaryDetails;

it('includes police, medical and hospitalization fields on aggressor', function (): void {
    expect((new Aggressor)->getFillable())->toContain(
        'has_police_reports',
        'police_report_count',
        'has_medical_reports',
        'medical_report_count',
        'hospitalization_days',
        'hospitalization_observations',
    );
});

it('does not include criminal history on beneficiary details', function (): void {
    expect((new BeneficiaryDetails)->getFillable())->not->toContain('criminal_history', 'criminal_history_notes');
});
