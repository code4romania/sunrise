<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Composers;

use App\Enums\FamilyRelationship;
use App\Enums\HomeType;
use App\Enums\SocialRelationship;
use App\Enums\Ternary;
use App\Models\InterventionMeeting;
use App\Models\InterventionService;
use App\Services\CaseExports\Support\ExportDataFormatter;
use Carbon\CarbonInterface;

class SocialCounselingSheetPdfComposer
{
    public function __construct(
        private readonly ExportDataFormatter $formatter,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function compose(InterventionService $service): array
    {
        $service->loadMissing([
            'interventionPlan.beneficiary',
            'interventionPlan.results.result',
            'interventionPlan.results.user',
            'interventionPlan.beneficiary.details',
            'interventionPlan.beneficiary.children',
            'interventionPlan.beneficiary.legal_residence.city',
            'interventionPlan.beneficiary.legal_residence.county',
            'interventionPlan.beneficiary.effective_residence.city',
            'interventionPlan.beneficiary.effective_residence.county',
            'specialist.user',
            'specialist.roleForDisplay',
            'organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition',
            'counselingSheet',
            'beneficiaryInterventions.organizationServiceIntervention.serviceInterventionWithoutStatusCondition',
            'beneficiaryInterventions.meetings.specialist.user',
        ]);

        $beneficiary = $service->interventionPlan?->beneficiary;
        $details = $beneficiary?->details;
        $sheetData = $service->counselingSheet?->data ?? [];

        $meetings = $service->beneficiaryInterventions
            ->flatMap(fn ($intervention) => $intervention->meetings->map(function (InterventionMeeting $meeting) use ($intervention): array {
                return [
                    'date_raw' => $meeting->date,
                    'time_raw' => $meeting->time,
                    'date' => $this->formatter->toPrintableValue($meeting->date),
                    'time' => $meeting->time instanceof CarbonInterface ? $meeting->time->format('H:i') : '—',
                    'intervention_name' => $intervention->organizationServiceIntervention?->serviceInterventionWithoutStatusCondition?->name ?? '—',
                    'topic' => $this->formatter->toPrintableValue($meeting->topic),
                    'observations' => $this->formatter->toPrintableValue($meeting->observations),
                    'duration' => $meeting->duration !== null ? (string) $meeting->duration : '—',
                ];
            }))
            ->sortBy([
                ['date_raw', 'asc'],
                ['time_raw', 'asc'],
            ])
            ->values()
            ->map(static function (array $row, int $index): array {
                $row['nr'] = $index + 1;
                unset($row['date_raw'], $row['time_raw']);

                return $row;
            })
            ->all();

        $interventionRows = $service->beneficiaryInterventions
            ->map(function ($intervention, int $index): array {
                return [
                    'nr' => $index + 1,
                    'name' => $intervention->organizationServiceIntervention?->serviceInterventionWithoutStatusCondition?->name ?? '—',
                    'objectives' => $this->formatter->toPrintableValue($intervention->objections),
                    'expected_results' => $this->formatter->toPrintableValue($intervention->expected_results),
                    'procedure' => $this->formatter->toPrintableValue($intervention->procedure),
                    'indicators' => $this->formatter->toPrintableValue($intervention->indicators),
                    'achievement_degree' => $this->formatter->toPrintableValue($intervention->achievement_degree),
                ];
            })
            ->values()
            ->all();

        $resultRows = $service->interventionPlan?->results
            ?->sortBy('started_at')
            ->values()
            ->map(function ($result, int $index): array {
                return [
                    'nr' => $index + 1,
                    'result_name' => $this->formatter->toPrintableValue($result->result?->name),
                    'specialist' => $this->formatter->toPrintableValue($result->user?->full_name),
                    'started_at' => $this->formatter->toPrintableValue($result->started_at),
                    'ended_at' => $this->formatter->toPrintableValue($result->ended_at),
                    'retried' => $result->retried === null
                        ? '—'
                        : ($result->retried ? __('general.yes') : __('general.no')),
                    'lost_from_monitoring' => $result->lost_from_monitoring === null
                        ? '—'
                        : ($result->lost_from_monitoring ? __('general.yes') : __('general.no')),
                    'observations' => $this->formatter->toPrintableValue($result->observations),
                ];
            })
            ->all() ?? [];

        $childrenRows = collect($beneficiary?->children ?? [])
            ->map(function ($child, int $index): array {
                return [
                    'nr' => $index + 1,
                    'name' => $this->formatter->toPrintableValue($child->name),
                    'gender' => $this->formatter->toPrintableValue($child->gender),
                    'birthdate' => $this->formatter->toPrintableValue($child->birthdate),
                    'age' => $this->formatter->toPrintableValue($child->age),
                    'locality' => $this->formatter->toPrintableValue($child->current_address),
                    'occupation' => $this->formatter->toPrintableValue($child->status),
                    'school_or_work' => $this->formatter->toPrintableValue($child->workspace),
                    'health_status' => '—',
                    'family_doctor' => '—',
                    'allowance' => '—',
                    'present_with_victim' => '—',
                    'identity_docs' => '—',
                    'paternity_recognized' => '—',
                    'schooling' => '—',
                    'relationship_characterization' => '—',
                ];
            })
            ->values()
            ->all();

        $familyRows = collect(data_get($sheetData, 'family', []))
            ->map(function (array $row): array {
                return [
                    'relationship' => $this->familyRelationshipLabel($row['relationship'] ?? null),
                    'name' => $this->formatter->toPrintableValue($row['first_and_last_name'] ?? null),
                    'age' => $this->formatter->toPrintableValue($row['age'] ?? null),
                    'locality' => $this->formatter->toPrintableValue($row['locality'] ?? null),
                    'occupation' => $this->formatter->toPrintableValue($row['occupation'] ?? null),
                    'relation_note' => $this->formatter->toPrintableValue($row['relationship_observation'] ?? null),
                    'support' => $this->ternaryLabel($row['support'] ?? null),
                    'support_observations' => $this->formatter->toPrintableValue($row['support_observations'] ?? null),
                ];
            })
            ->values()
            ->all();

        $supportGroupRows = collect(data_get($sheetData, 'support_group', []))
            ->map(function (array $row): array {
                return [
                    'relationship' => $this->socialRelationshipLabel($row['relationship'] ?? null),
                    'name' => $this->formatter->toPrintableValue($row['full_name'] ?? null),
                    'support' => $this->ternaryLabel($row['support'] ?? null),
                    'support_observations' => $this->formatter->toPrintableValue($row['support_observations'] ?? null),
                ];
            })
            ->values()
            ->all();

        return [
            'institution' => $service->institution ?? '—',
            'provider_name' => $service->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? '—',
            'sheet_date' => $this->formatter->toPrintableValue($this->firstFilledString($sheetData, ['sheet_date', 'date_written', 'data_intocmirii_fisei']) ?? now()),
            'specialist_name' => $service->specialist?->user?->full_name ?? '—',
            'case_number' => $beneficiary?->id ?? '—',
            'signature' => '........................................',

            'victim_name' => $beneficiary?->full_name ?? '—',
            'victim_birthdate' => $this->formatter->toPrintableValue($beneficiary?->birthdate),
            'victim_locality' => $this->formatter->toPrintableValue($beneficiary?->effective_residence?->city?->name),
            'victim_civil_status' => $this->formatter->toPrintableValue($beneficiary?->civil_status),
            'victim_prior_name' => $this->formatter->toPrintableValue($beneficiary?->prior_name),
            'victim_current_relationship' => $this->formatter->toPrintableValue(data_get($sheetData, 'current_relationship')),
            'victim_legal_address' => $this->formatAddress($beneficiary?->legal_residence?->address, $beneficiary?->legal_residence?->city?->name, $beneficiary?->legal_residence?->county?->name),
            'victim_effective_address' => $this->formatAddress($beneficiary?->effective_residence?->address, $beneficiary?->effective_residence?->city?->name, $beneficiary?->effective_residence?->county?->name),
            'family_doctor' => Ternary::isYes($details?->has_family_doctor) ? 'Da' : (Ternary::isNo($details?->has_family_doctor) ? 'Nu' : '—'),
            'family_doctor_details' => trim(implode(' / ', array_filter([
                $details?->family_doctor_name,
                $details?->family_doctor_address,
                $details?->family_doctor_contact,
            ], static fn (?string $v): bool => filled($v)))) ?: '—',
            'health_status' => $this->formatter->toPrintableValue($details?->health_status),
            'health_chronic' => $this->formatter->toPrintableValue($details?->observations_chronic_diseases),
            'health_degenerative' => $this->formatter->toPrintableValue($details?->observations_degenerative_diseases),
            'health_psychic' => $this->formatter->toPrintableValue($details?->observations_mental_illness),
            'disability' => Ternary::isYes($details?->disabilities) ? 'Da' : (Ternary::isNo($details?->disabilities) ? 'Nu' : '—'),
            'disability_type' => $this->formatter->toPrintableValue($details?->type_of_disability),
            'disability_degree' => $this->formatter->toPrintableValue($details?->degree_of_disability),
            'health_insurance' => Ternary::isYes($details?->health_insurance) ? 'Da' : (Ternary::isNo($details?->health_insurance) ? 'Nu' : '—'),
            'studies' => $this->formatter->toPrintableValue($details?->studies),
            'workplace' => $this->formatter->toPrintableValue($details?->workplace),
            'occupation' => $this->formatter->toPrintableValue($details?->occupation),
            'professional_experience' => $this->formatter->toPrintableValue(data_get($sheetData, 'professional_experience')),
            'victim_contacts' => trim(implode(' / ', array_filter([
                $beneficiary?->primary_phone,
                $beneficiary?->backup_phone,
                $beneficiary?->social_media,
                $beneficiary?->email,
            ], static fn (?string $v): bool => filled($v)))) ?: '—',
            'emergency_contact_exists' => (filled($beneficiary?->contact_person_name) || filled($beneficiary?->contact_person_phone)) ? 'Da' : 'Nu',
            'emergency_contact_name' => $this->formatter->toPrintableValue($beneficiary?->contact_person_name),
            'emergency_contact_phone' => $this->formatter->toPrintableValue($beneficiary?->contact_person_phone),

            'net_income' => $this->formatter->toPrintableValue($details?->net_income),
            'income_source' => $this->formatter->toPrintableValue($details?->income_source),
            'social_benefits_notes' => $this->formatter->toPrintableValue(data_get($sheetData, 'benefits_observations')),
            'payment_method' => $this->formatter->toPrintableValue(data_get($sheetData, 'payment_method')),

            'family_rows' => $familyRows,
            'support_group_rows' => $supportGroupRows,

            'home_type' => $this->homeTypeLabel(data_get($sheetData, 'home_type')),
            'rooms' => $this->formatter->toPrintableValue(data_get($sheetData, 'rooms')),
            'peoples' => $this->formatter->toPrintableValue(data_get($sheetData, 'peoples')),
            'utilities' => $this->formatter->toPrintableValue(data_get($sheetData, 'utilities')),
            'living_observations' => $this->formatter->toPrintableValue(data_get($sheetData, 'living_observations')),

            'children_total' => $beneficiary?->children_total_count ?? count($childrenRows),
            'children_in_care' => $beneficiary?->children_care_count ?? count($childrenRows),
            'children_protection_measure' => $this->formatter->toPrintableValue(data_get($sheetData, 'children_protection_measure')),
            'children_other_care' => $this->formatter->toPrintableValue(data_get($sheetData, 'children_other_care')),
            'children_present_with_victim' => $beneficiary?->children_accompanying_count ?? '—',
            'children_rows' => $childrenRows,

            'communication' => $this->ternaryLabel(data_get($sheetData, 'communication')),
            'socialization' => $this->ternaryLabel(data_get($sheetData, 'socialization')),
            'rules_compliance' => $this->ternaryLabel(data_get($sheetData, 'rules_compliance')),
            'participation_in_individual_counseling' => $this->ternaryLabel(data_get($sheetData, 'participation_in_individual_counseling')),
            'participation_in_joint_activities' => $this->ternaryLabel(data_get($sheetData, 'participation_in_joint_activities')),
            'self_management' => $this->ternaryLabel(data_get($sheetData, 'self_management')),
            'addictive_behavior' => $this->ternaryLabel(data_get($sheetData, 'addictive_behavior')),
            'financial_education' => $this->ternaryLabel(data_get($sheetData, 'financial_education')),
            'integration_observations' => $this->formatter->toPrintableValue(data_get($sheetData, 'integration_and_participation_in_social_service_observations')),

            'intervention_rows' => $interventionRows,
            'result_rows' => $resultRows,
            'meetings_rows' => $meetings,
        ];
    }

    private function firstFilledString(array $sheetData, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = data_get($sheetData, $key);
            if ($value === null) {
                continue;
            }

            $string = trim((string) $value);
            if ($string !== '') {
                return $string;
            }
        }

        return null;
    }

    private function formatAddress(?string $address, ?string $city, ?string $county): string
    {
        $parts = array_filter([$address, $city, $county], static fn (?string $value): bool => filled($value));

        return $parts !== [] ? implode(', ', $parts) : '—';
    }

    private function ternaryLabel(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if ($value instanceof Ternary) {
            return $value->getLabel();
        }

        if (is_numeric($value)) {
            $enum = Ternary::tryFrom((int) $value);

            return $enum?->getLabel() ?? $this->formatter->toPrintableValue($value);
        }

        $stringValue = trim((string) $value);
        if ($stringValue === '') {
            return '—';
        }

        if (in_array(strtolower($stringValue), ['yes', 'da', 'true'], true)) {
            return Ternary::YES->getLabel();
        }

        if (in_array(strtolower($stringValue), ['no', 'nu', 'false'], true)) {
            return Ternary::NO->getLabel();
        }

        if ($stringValue === '-1') {
            return Ternary::UNKNOWN->getLabel();
        }

        return $this->formatter->toPrintableValue($value);
    }

    private function homeTypeLabel(mixed $value): string
    {
        if ($value instanceof HomeType) {
            return $value->getLabel();
        }

        $enum = HomeType::tryFrom((string) $value);

        return $enum?->getLabel() ?? $this->formatter->toPrintableValue($value);
    }

    private function familyRelationshipLabel(mixed $value): string
    {
        if ($value instanceof FamilyRelationship) {
            return $value->getLabel();
        }

        $enum = FamilyRelationship::tryFrom((string) $value);

        return $enum?->getLabel() ?? $this->formatter->toPrintableValue($value);
    }

    private function socialRelationshipLabel(mixed $value): string
    {
        if ($value instanceof SocialRelationship) {
            return $value->getLabel();
        }

        $enum = SocialRelationship::tryFrom((string) $value);

        return $enum?->getLabel() ?? $this->formatter->toPrintableValue($value);
    }
}
