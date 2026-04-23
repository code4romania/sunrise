<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Composers;

use App\Enums\FileDocumentType;
use App\Enums\Patrimony;
use App\Enums\PossessionMode;
use App\Models\InterventionMeeting;
use App\Models\InterventionService;
use App\Services\CaseExports\Support\ExportDataFormatter;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class LegalCounselingSheetPdfComposer
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
            'specialist.user',
            'specialist.roleForDisplay',
            'counselingSheet',
            'organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition',
            'beneficiaryInterventions.meetings.specialist.user',
            'beneficiaryInterventions.organizationServiceIntervention.serviceInterventionWithoutStatusCondition',
        ]);

        $beneficiary = $service->interventionPlan?->beneficiary;
        $sheetData = $service->counselingSheet?->data ?? [];

        $meetingsRows = $this->buildMeetingsRows($sheetData, $service);

        $institutions = data_get($sheetData, 'institutions', []);
        $institutions = is_array($institutions) ? array_slice($institutions, 0, 3) : [];
        while (count($institutions) < 3) {
            $institutions[] = ['institution' => '', 'phone' => '', 'contact_person' => ''];
        }

        $copyDocuments = collect(data_get($sheetData, 'copy_documents', []))->map(static fn ($v): string => (string) $v)->all();
        $originalDocuments = collect(data_get($sheetData, 'original_documents', []))->map(static fn ($v): string => (string) $v)->all();

        return [
            'service_name' => $service->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? '—',
            'sheet_date' => $this->formatter->toPrintableValue($this->firstFilledString($sheetData, ['sheet_date', 'date_written', 'data_intocmirii_fisei']) ?? now()),
            'specialist_name' => $service->specialist?->user?->full_name ?? '—',
            'case_number' => (string) ($beneficiary?->id ?? '—'),
            'beneficiary_name' => $beneficiary?->full_name ?? '—',
            'patrimony' => (string) data_get($sheetData, 'patrimony', ''),
            'possession_mode' => (string) data_get($sheetData, 'possession_mode', ''),
            'possession_observation' => $this->formatter->toPrintableValue(data_get($sheetData, 'possession_observation')),
            'copy_documents' => $copyDocuments,
            'original_documents' => $originalDocuments,
            'copy_documents_observation' => $this->formatter->toPrintableValue(data_get($sheetData, 'copy_documents_observation')),
            'original_documents_observation' => $this->formatter->toPrintableValue(data_get($sheetData, 'original_documents_observation')),
            'institutions' => $institutions,
            'meetings_rows' => $meetingsRows,
            'patrimony_checks' => [
                'apartment' => (string) data_get($sheetData, 'patrimony', '') === Patrimony::APARTMENT->value,
                'house' => (string) data_get($sheetData, 'patrimony', '') === Patrimony::HOUSE->value,
                'without' => (string) data_get($sheetData, 'patrimony', '') === Patrimony::WITHOUT->value,
            ],
            'possession_checks' => [
                'exclusive_property' => (string) data_get($sheetData, 'possession_mode', '') === PossessionMode::EXCLUSIVE_PROPERTY->value,
                'co_ownership' => (string) data_get($sheetData, 'possession_mode', '') === PossessionMode::CO_OWNERSHIP->value,
                'rental_state_housing' => (string) data_get($sheetData, 'possession_mode', '') === PossessionMode::RENTAL_STATE_HOUSING->value,
                'private_housing_rental' => (string) data_get($sheetData, 'possession_mode', '') === PossessionMode::PRIVATE_HOUSING_RENTAL->value,
                'commode' => (string) data_get($sheetData, 'possession_mode', '') === PossessionMode::COMMODE->value,
                'donation' => (string) data_get($sheetData, 'possession_mode', '') === PossessionMode::DONATION->value,
                'other' => (string) data_get($sheetData, 'possession_mode', '') === PossessionMode::OTHER->value,
            ],
            'documents_checks' => [
                FileDocumentType::MARRIAGE_CERTIFICATE->value => in_array(FileDocumentType::MARRIAGE_CERTIFICATE->value, $copyDocuments, true) || in_array(FileDocumentType::MARRIAGE_CERTIFICATE->value, $originalDocuments, true),
                FileDocumentType::CHILDREN_BIRTH_CERTIFICATE->value => in_array(FileDocumentType::CHILDREN_BIRTH_CERTIFICATE->value, $copyDocuments, true) || in_array(FileDocumentType::CHILDREN_BIRTH_CERTIFICATE->value, $originalDocuments, true),
                FileDocumentType::LAND_DEED_EXTRACT->value => in_array(FileDocumentType::LAND_DEED_EXTRACT->value, $copyDocuments, true) || in_array(FileDocumentType::LAND_DEED_EXTRACT->value, $originalDocuments, true),
                FileDocumentType::RENTAL_AGREEMENT->value => in_array(FileDocumentType::RENTAL_AGREEMENT->value, $copyDocuments, true) || in_array(FileDocumentType::RENTAL_AGREEMENT->value, $originalDocuments, true),
                FileDocumentType::SALE_PURCHASE_AGREEMENT->value => in_array(FileDocumentType::SALE_PURCHASE_AGREEMENT->value, $copyDocuments, true) || in_array(FileDocumentType::SALE_PURCHASE_AGREEMENT->value, $originalDocuments, true),
                FileDocumentType::IML_CERTIFICATE->value => in_array(FileDocumentType::IML_CERTIFICATE->value, $copyDocuments, true) || in_array(FileDocumentType::IML_CERTIFICATE->value, $originalDocuments, true),
                FileDocumentType::OTHER->value => in_array(FileDocumentType::OTHER->value, $copyDocuments, true) || in_array(FileDocumentType::OTHER->value, $originalDocuments, true),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $sheetData
     * @return list<array<string, string|int>>
     */
    private function buildMeetingsRows(array $sheetData, InterventionService $service): array
    {
        $rows = $service->beneficiaryInterventions
            ->flatMap(fn ($intervention) => $intervention->meetings->map(function (InterventionMeeting $meeting) use ($intervention): array {
                return [
                    'date_raw' => $meeting->date,
                    'time_raw' => $meeting->time,
                    'date' => $this->formatter->toPrintableValue($meeting->date),
                    'time' => $meeting->time?->format('H:i') ?? '—',
                    'intervention_name' => $intervention->organizationServiceIntervention?->serviceInterventionWithoutStatusCondition?->name ?? '—',
                    'specialist' => $meeting->specialist?->name_role ?? '—',
                    'duration' => $meeting->duration !== null ? (string) $meeting->duration : '—',
                    'topic' => $this->formatter->toPrintableValue($meeting->topic),
                    'observations' => $this->formatter->toPrintableValue($meeting->observations),
                    'details' => $this->formatter->toPrintableValue($meeting->observations),
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

        if ($rows !== []) {
            return $rows;
        }

        return collect($this->extractSessions($sheetData, $service))
            ->map(function (array $session, int $index): array {
                return [
                    'nr' => $index + 1,
                    'date' => $this->formatter->toPrintableValue(data_get($session, 'date')),
                    'time' => $this->formatter->toPrintableValue(data_get($session, 'time')),
                    'intervention_name' => $this->formatter->toPrintableValue(data_get($session, 'intervention_name')),
                    'specialist' => '—',
                    'duration' => $this->formatter->toPrintableValue(data_get($session, 'duration')),
                    'topic' => $this->formatter->toPrintableValue(data_get($session, 'topic')),
                    'observations' => $this->formatter->toPrintableValue(data_get($session, 'observations')),
                    'details' => $this->formatter->toPrintableValue(
                        data_get($session, 'details', data_get($session, 'observations'))
                    ),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $sheetData
     * @return list<array<string, mixed>>
     */
    private function extractSessions(array $sheetData, InterventionService $service): array
    {
        $keys = ['legal_sessions', 'sessions', 'section_3_sessions', 'juridical_sessions'];

        foreach ($keys as $key) {
            $raw = data_get($sheetData, $key);
            if (is_array($raw) && $raw !== []) {
                return array_values($raw);
            }
        }

        return $service->beneficiaryInterventions
            ->flatMap(fn ($intervention) => $intervention->meetings->map(static function (InterventionMeeting $meeting): array {
                return [
                    'date' => $meeting->date?->format('d/m/Y'),
                    'time' => $meeting->time instanceof CarbonInterface
                        ? $meeting->time->format('H:i')
                        : ($meeting->time !== null ? Carbon::parse((string) $meeting->time)->format('H:i') : null),
                    'duration' => $meeting->duration,
                    'topic' => $meeting->topic,
                    'details' => $meeting->observations,
                    'observations' => $meeting->observations,
                    'session_number' => null,
                    'place' => 'center',
                    'type' => 'informare',
                ];
            }))
            ->values()
            ->all();
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
