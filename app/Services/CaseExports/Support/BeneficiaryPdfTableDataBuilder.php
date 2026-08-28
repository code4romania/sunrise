<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Support;

use App\Enums\Citizenship;
use App\Enums\CivilStatus;
use App\Enums\Occupation;
use App\Enums\ResidenceEnvironment;
use App\Enums\Studies;
use App\Models\Beneficiary;

class BeneficiaryPdfTableDataBuilder
{
    public function __construct(
        private readonly ExportDataFormatter $formatter,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function buildBeneficiaryIdentityTableData(Beneficiary $beneficiary): array
    {
        $details = $beneficiary->details;
        $legalResidence = $beneficiary->legal_residence;
        $effectiveResidence = $beneficiary->effective_residence;

        return [
            'first_name' => $beneficiary->first_name,
            'last_name' => $beneficiary->last_name,
            'prior_name' => $beneficiary->prior_name,
            'birthdate' => $beneficiary->birthdate?->format('d.m.Y') ?? '—',
            'birthplace' => $beneficiary->birthplace,
            'cnp' => $beneficiary->cnp,
            'id_type' => $this->formatter->toPrintableValue($beneficiary->id_type),
            'id_serial' => $beneficiary->id_serial,
            'id_number' => $beneficiary->id_number,
            'primary_phone' => $beneficiary->primary_phone,
            'backup_phone' => $beneficiary->backup_phone,
            'email' => $beneficiary->email,
            'social_media' => $beneficiary->social_media,
            'contact_notes' => $beneficiary->contact_notes,

            'citizenship_is_romanian' => $beneficiary->citizenship instanceof Citizenship
                && $beneficiary->citizenship === Citizenship::ROMANIAN,
            'citizenship_is_other' => $beneficiary->citizenship instanceof Citizenship
                && $beneficiary->citizenship !== Citizenship::ROMANIAN,

            'civil_status_value' => $beneficiary->civil_status instanceof CivilStatus ? $beneficiary->civil_status->value : null,
            'studies_value' => $details?->studies instanceof Studies ? $details->studies->value : null,
            'occupation_value' => $details?->occupation instanceof Occupation ? $details->occupation->value : null,

            'legal_environment' => $legalResidence?->environment instanceof ResidenceEnvironment ? $legalResidence->environment->value : null,
            'effective_environment' => $effectiveResidence?->environment instanceof ResidenceEnvironment ? $effectiveResidence->environment->value : null,

            'legal_address' => $legalResidence?->address,
            'effective_address' => $effectiveResidence?->address,
        ];
    }

    /**
     * @return array{
     *     total: int|string,
     *     care: int|string,
     *     sub10: int,
     *     tenTo18: int,
     *     over18: int,
     *     rows: array<int, array{name: string, age: mixed, current_address: mixed, status: mixed, observations: mixed}>
     * }
     */
    public function buildChildrenTableData(Beneficiary $beneficiary): array
    {
        $children = $beneficiary->children;

        $totalChildren = $beneficiary->children_total_count ?? $children->count();
        $careChildren = $beneficiary->children_care_count ?? $children->count();

        $sub10Care = $children->filter(function ($child): bool {
            $age = $child->age;
            if ($age === '<1') {
                return true;
            }

            return is_int($age) ? $age < 10 : false;
        })->count();

        $tenTo18Care = $children->filter(function ($child): bool {
            $age = $child->age;
            if ($age === '<1') {
                return false;
            }

            if (! is_int($age)) {
                return false;
            }

            return $age >= 10 && $age <= 18;
        })->count();

        $over18Care = $children->filter(function ($child): bool {
            $age = $child->age;
            if ($age === '<1') {
                return false;
            }

            return is_int($age) ? $age > 18 : false;
        })->count();

        $childrenRows = $children->map(function ($child): array {
            return [
                'name' => (string) ($child->name ?? ''),
                'age' => $child->age ?? '—',
                'current_address' => $child->current_address ?? '—',
                'status' => $child->status ?? '—',
                'observations' => $child->workspace ?? '—',
            ];
        })->values()->all();

        return [
            'total' => $totalChildren,
            'care' => $careChildren,
            'sub10' => $sub10Care,
            'tenTo18' => $tenTo18Care,
            'over18' => $over18Care,
            'rows' => $childrenRows,
        ];
    }
}
