<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Composers;

use App\Enums\Drug;
use App\Enums\ExtendedFrequency;
use App\Enums\Ternary;
use App\Models\InterventionMeeting;
use App\Models\InterventionService;
use App\Services\CaseExports\Support\ExportDataFormatter;

class PsychologicalCounselingSheetPdfComposer
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
            'interventionPlan.beneficiary.children',
            'interventionPlan.beneficiary.details',
            'specialist.user',
            'specialist.roleForDisplay',
            'counselingSheet',
            'organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition',
            'beneficiaryInterventions.organizationServiceIntervention.serviceInterventionWithoutStatusCondition',
            'beneficiaryInterventions.meetings.specialist.user',
            'beneficiaryInterventions.meetings.specialist.roleForDisplay',
        ]);

        $beneficiary = $service->interventionPlan?->beneficiary;
        $details = $beneficiary?->details;
        $sheetData = $service->counselingSheet?->data ?? [];
        $serviceName = $service->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? '—';
        $specialist = $service->specialist;

        $section27RowsFromInterventions = $service->beneficiaryInterventions
            ->map(function ($intervention, int $index): array {
                $interventionName = $intervention->organizationServiceIntervention?->serviceInterventionWithoutStatusCondition?->name ?? '—';
                $summaryParts = array_filter([
                    $intervention->objections,
                    $intervention->expected_results,
                    $intervention->procedure,
                    $intervention->indicators,
                    $intervention->achievement_degree,
                ], static fn (?string $value): bool => $value !== null && trim($value) !== '');

                $summary = $summaryParts === []
                    ? '—'
                    : implode("\n\n", array_map(static fn (string $value): string => trim(strip_tags($value)), $summaryParts));

                return [
                    'nr' => $index + 1,
                    'date' => '—',
                    'session_number' => '—',
                    'intervention_name' => $interventionName,
                    'summary' => $summary,
                ];
            })
            ->values()
            ->all();

        $meetings = $service->beneficiaryInterventions
            ->flatMap(fn ($intervention) => $intervention->meetings->map(function (InterventionMeeting $meeting) use ($intervention): array {
                return [
                    'date_raw' => $meeting->date,
                    'time_raw' => $meeting->time,
                    'date' => $this->formatter->toPrintableValue($meeting->date),
                    'time' => $meeting->time?->format('H:i') ?? '—',
                    'intervention_name' => $intervention->organizationServiceIntervention?->serviceInterventionWithoutStatusCondition?->name ?? '—',
                    'specialist' => $meeting->specialist?->name_role ?? '—',
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

        $lastMeeting = $meetings === [] ? null : $meetings[array_key_last($meetings)];
        $childrenRows = collect($beneficiary?->children ?? [])
            ->map(function ($child): array {
                return [
                    'name' => $child->name ?? '—',
                    'age' => $child->age ?? '—',
                    'school_or_work' => $child->current_address ?? '—',
                    'class' => $child->status ?? '—',
                ];
            })
            ->values()
            ->all();

        $drugTypes = collect($details?->drug_types ?? [])->map(static function ($drug): string {
            if ($drug instanceof Drug) {
                return $drug->value;
            }

            return (string) $drug;
        })->all();

        $frequencyRows = [
            ['label' => 'Fizică', 'value' => (string) data_get($sheetData, 'physics', '')],
            ['label' => 'Sexuală', 'value' => (string) data_get($sheetData, 'sexed', '')],
            ['label' => 'Psihologică (Emoțională)', 'value' => (string) data_get($sheetData, 'psychological', '')],
            ['label' => 'Verbală', 'value' => (string) data_get($sheetData, 'verbal', '')],
            ['label' => 'Socială', 'value' => (string) data_get($sheetData, 'sociable', '')],
            ['label' => 'Economică', 'value' => (string) data_get($sheetData, 'economic', '')],
            ['label' => 'Cibernetică', 'value' => (string) data_get($sheetData, 'cybernetics', '')],
            ['label' => 'Spirituală', 'value' => (string) data_get($sheetData, 'spiritual', '')],
        ];

        $section27RowsFromSheet = $this->extractSection27RowsFromSheet($sheetData);
        $section27Rows = $section27RowsFromSheet !== []
            ? $section27RowsFromSheet
            : $section27RowsFromInterventions;

        $section13FromSheet = $this->firstFilledString($sheetData, [
            'section_13',
            'section13',
            'plan_intervention_recommendations',
            'plan_de_interventie_recomandari',
            'recommendations_for_plan',
        ]);

        $section13 = $section13FromSheet ?? collect($section27Rows)->map(static fn (array $row): string => (string) ($row['summary'] ?? ''))
            ->filter(static fn (string $text): bool => trim($text) !== '' && $text !== '—')
            ->implode("\n\n");

        $section15FromSheet = $this->firstFilledString($sheetData, [
            'section_15',
            'section15',
            'recommendations',
            'recomandari',
            'observatii_recomandari',
        ]);

        $section15 = $section15FromSheet ?? collect($meetings)->map(static fn (array $row): string => (string) ($row['observations'] ?? ''))
            ->filter(static fn (string $text): bool => trim($text) !== '' && $text !== '—')
            ->implode("\n\n");

        $sheetSessionNumber = $this->firstFilledString($sheetData, [
            'session_number',
            'numar_sedinta',
            'number_of_session',
        ]);

        $sheetDate = $this->firstFilledString($sheetData, [
            'sheet_date',
            'date_written',
            'data_intocmirii_fisei',
        ]);

        $sheetScheduleDate = $this->firstFilledString($sheetData, [
            'programare_date',
            'schedule_date',
            'next_meeting_date',
        ]);
        $sheetScheduleTime = $this->firstFilledString($sheetData, [
            'programare_time',
            'schedule_time',
            'next_meeting_time',
        ]);

        $safetyPlanFromSheet = $this->firstFilledString($sheetData, [
            'section_14',
            'section14',
            'safety_plan',
            'plan_de_siguranta',
        ]);
        $hasSafetyPlan = $safetyPlanFromSheet !== null
            ? in_array(strtolower($safetyPlanFromSheet), ['1', 'yes', 'da', 'true'], true)
            : str_contains(mb_strtolower((string) $section13), 'siguran');

        return [
            'service_name' => $serviceName,
            'sheet_date' => $sheetDate ?? now()->format('d.m.Y'),
            'beneficiary_name' => $beneficiary?->full_name ?? '—',
            'case_number' => $beneficiary?->id ?? '—',
            'specialist_name' => $specialist?->user?->full_name ?? '—',
            'specialist_role' => $specialist?->roleForDisplay?->name ?? '—',
            'session_number' => $sheetSessionNumber ?? (string) max(count($meetings), 1),
            'duration_60' => ($lastMeeting['duration'] ?? null) === '60',
            'duration_90' => ($lastMeeting['duration'] ?? null) === '90',
            'duration_120' => ($lastMeeting['duration'] ?? null) === '120',
            'civil_single' => (string) ($beneficiary?->civil_status?->value ?? '') === 'single',
            'civil_cohabitation' => (string) ($beneficiary?->civil_status?->value ?? '') === 'cohabitation',
            'civil_married' => (string) ($beneficiary?->civil_status?->value ?? '') === 'married',
            'civil_divorced' => (string) ($beneficiary?->civil_status?->value ?? '') === 'divorced',
            'civil_widowed' => (string) ($beneficiary?->civil_status?->value ?? '') === 'widowed',
            'children_count' => (string) ($beneficiary?->children_total_count ?? $beneficiary?->children?->count() ?? 0),
            'children_rows' => $childrenRows,
            'drug_alcohol_occasional' => in_array(Drug::ALCOHOL_OCCASIONAL->value, $drugTypes, true),
            'drug_alcohol_frequent' => in_array(Drug::ALCOHOL_FREQUENT->value, $drugTypes, true),
            'drug_tobacco' => in_array(Drug::TOBACCO->value, $drugTypes, true),
            'drug_tranquilizers' => in_array(Drug::TRANQUILIZERS->value, $drugTypes, true),
            'drug_drugs' => in_array(Drug::DRUGS->value, $drugTypes, true),
            'drug_other' => in_array(Drug::OTHER->value, $drugTypes, true),
            'drug_other_text' => $this->formatter->toPrintableValue($details?->medication_observations),
            'current_contraception_yes' => Ternary::isYes($details?->current_contraception),
            'current_contraception_no' => Ternary::isNo($details?->current_contraception),
            'current_contraception_text' => $this->formatter->toPrintableValue($details?->observations_contraception),
            'psychiatric_history_yes' => Ternary::isYes($details?->psychiatric_history),
            'psychiatric_history_no' => Ternary::isNo($details?->psychiatric_history),
            'psychiatric_history_text' => $this->formatter->toPrintableValue($details?->psychiatric_history_notes),
            'investigations_yes' => Ternary::isYes($details?->investigations_for_psychiatric_pathology),
            'investigations_no' => Ternary::isNo($details?->investigations_for_psychiatric_pathology),
            'investigations_text' => $this->formatter->toPrintableValue($details?->investigations_observations),
            'treatment_yes' => Ternary::isYes($details?->treatment_for_psychiatric_pathology),
            'treatment_no' => Ternary::isNo($details?->treatment_for_psychiatric_pathology),
            'treatment_text' => $this->formatter->toPrintableValue($details?->treatment_observations),
            'section_3' => $this->formatter->toPrintableValue(data_get($sheetData, 'relationship_history')),
            'section_4' => $this->formatter->toPrintableValue(data_get($sheetData, 'last_incident_description')),
            'section_5' => $this->formatter->toPrintableValue(data_get($sheetData, 'violence_history_forms')),
            'frequency_rows' => collect($frequencyRows)->map(function (array $row): array {
                $value = $row['value'];

                return [
                    'label' => $row['label'],
                    'daily' => $value === ExtendedFrequency::DAILY->value,
                    'weekly' => $value === ExtendedFrequency::WEEKLY->value,
                    'monthly' => $value === ExtendedFrequency::MONTHLY->value,
                    'rare' => $value === ExtendedFrequency::LASS_THAN_MONTHLY->value,
                ];
            })->all(),
            'description_physical' => $this->formatter->toPrintableValue(data_get($sheetData, 'physical_violence_description')),
            'description_sexual' => $this->formatter->toPrintableValue(data_get($sheetData, 'sexual_violence_description')),
            'description_psychological' => $this->formatter->toPrintableValue(data_get($sheetData, 'psychological_violence_description')),
            'description_verbal' => $this->formatter->toPrintableValue(data_get($sheetData, 'verbal_violence_description')),
            'description_social' => $this->formatter->toPrintableValue(data_get($sheetData, 'social_violence_description')),
            'description_economic' => $this->formatter->toPrintableValue(data_get($sheetData, 'economic_violence_description')),
            'description_cyber' => $this->formatter->toPrintableValue(data_get($sheetData, 'cyber_violence_description')),
            'description_spiritual' => $this->formatter->toPrintableValue(data_get($sheetData, 'spiritual_violence_description')),
            'effects_physical' => $this->formatter->toPrintableValue(data_get($sheetData, 'physical_effects')),
            'effects_psychological' => $this->formatter->toPrintableValue(data_get($sheetData, 'psychological_effects')),
            'effects_social' => $this->formatter->toPrintableValue(data_get($sheetData, 'social_effects')),
            'section_9' => $this->formatter->toPrintableValue(data_get($sheetData, 'risk_factors_description')),
            'section_10_internal' => $this->formatter->toPrintableValue(data_get($sheetData, 'internal_resources')),
            'section_10_external' => $this->formatter->toPrintableValue(data_get($sheetData, 'external_resources')),
            'section_11' => $this->formatter->toPrintableValue(data_get($sheetData, 'requests_description')),
            'section_12' => $this->formatter->toPrintableValue(data_get($sheetData, 'first_meeting_psychological_evaluation')),
            'section_13' => $section13 !== '' ? $section13 : '—',
            'section_14_yes' => $hasSafetyPlan,
            'section_14_no' => ! $hasSafetyPlan,
            'section_15' => $section15 !== '' ? $section15 : '—',
            'schedule_date' => $sheetScheduleDate ?? ($lastMeeting['date'] ?? '—'),
            'schedule_time' => $sheetScheduleTime ?? ($lastMeeting['time'] ?? '—'),
            'section_27_rows' => $section27Rows,
            'meetings_rows' => $meetings,
        ];
    }

    /**
     * @param  array<string, mixed>  $sheetData
     * @return list<array{nr:int,date:string,session_number:string,intervention_name:string,summary:string}>
     */
    private function extractSection27RowsFromSheet(array $sheetData): array
    {
        $candidateKeys = [
            'section_27',
            'section27',
            'section_27_plan',
            'section_27_observations',
            'plan_counseling_details',
            'plan_consiliere',
            'observatii_interventie_detaliere',
        ];

        foreach ($candidateKeys as $key) {
            $raw = data_get($sheetData, $key);
            if (! is_array($raw) || $raw === []) {
                continue;
            }

            $rows = collect($raw)->map(function ($row, int $index): array {
                if (! is_array($row)) {
                    return [
                        'nr' => $index + 1,
                        'date' => '—',
                        'session_number' => '—',
                        'intervention_name' => '—',
                        'summary' => $this->formatter->toPrintableValue($row),
                    ];
                }

                return [
                    'nr' => $index + 1,
                    'date' => $this->formatter->toPrintableValue($row['date'] ?? $row['data'] ?? null),
                    'session_number' => $this->formatter->toPrintableValue($row['session_number'] ?? $row['numar_sedinta'] ?? null),
                    'intervention_name' => $this->formatter->toPrintableValue($row['intervention_name'] ?? $row['intervention'] ?? $row['title'] ?? null),
                    'summary' => $this->formatter->toPrintableValue($row['summary'] ?? $row['details'] ?? $row['observations'] ?? null),
                ];
            })->values()->all();

            if ($rows !== []) {
                return $rows;
            }
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $sheetData
     * @param  list<string>  $keys
     */
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
}
