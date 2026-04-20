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

        $sessions = $this->extractSessions($sheetData, $service);
        $sessionSlots = array_slice(array_pad($sessions, 10, []), 0, 10);

        $institutions = data_get($sheetData, 'institutions', []);
        $institutions = is_array($institutions) ? array_slice($institutions, 0, 3) : [];
        while (count($institutions) < 3) {
            $institutions[] = ['institution' => '', 'phone' => '', 'contact_person' => ''];
        }

        $copyDocuments = collect(data_get($sheetData, 'copy_documents', []))->map(static fn ($v): string => (string) $v)->all();
        $originalDocuments = collect(data_get($sheetData, 'original_documents', []))->map(static fn ($v): string => (string) $v)->all();

        $detailsRows = $this->extractDetailsRows($sheetData, $sessionSlots);

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
            'matrix_columns' => range(1, 10),
            'matrix_rows' => $this->buildMatrixRows($sessionSlots),
            'section4_rows' => $detailsRows,
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
                    'date' => $meeting->date?->format('d.m.Y'),
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
     * @param  list<array<string, mixed>>  $sessionSlots
     * @return list<array{label:string, values:list<string>}>
     */
    private function buildMatrixRows(array $sessionSlots): array
    {
        $rows = [
            ['label' => 'Data', 'type' => 'text', 'key' => 'date'],
            ['label' => 'Locul consilierii - Centru', 'type' => 'bool', 'keys' => ['place', 'location'], 'value' => 'center'],
            ['label' => 'Locul consilierii - Adăpost', 'type' => 'bool', 'keys' => ['place', 'location'], 'value' => 'shelter'],
            ['label' => 'Tipul consilierii - Informare', 'type' => 'bool', 'keys' => ['type'], 'value' => 'informare'],
            ['label' => 'Tipul consilierii - Întocmire acte juridice', 'type' => 'bool', 'keys' => ['type'], 'value' => 'intocmire_acte'],
            ['label' => 'Conținut - Plângere Poliție', 'type' => 'array', 'keys' => ['content', 'content_items'], 'value' => 'plangere_politie'],
            ['label' => 'Conținut - Plângere penală', 'type' => 'array', 'keys' => ['content', 'content_items'], 'value' => 'plangere_penala'],
            ['label' => 'Conținut - Ordin de protecție', 'type' => 'array', 'keys' => ['content', 'content_items'], 'value' => 'ordin_protectie'],
            ['label' => 'Conținut - Divorț', 'type' => 'array', 'keys' => ['content', 'content_items'], 'value' => 'divort'],
            ['label' => 'Conținut - Evacuare', 'type' => 'array', 'keys' => ['content', 'content_items'], 'value' => 'evacuare'],
            ['label' => 'Durata 30 minute', 'type' => 'duration', 'value' => 30],
            ['label' => 'Durata 40 minute', 'type' => 'duration', 'value' => 40],
            ['label' => 'Durata 50 minute', 'type' => 'duration', 'value' => 50],
            ['label' => 'Durata 60 minute', 'type' => 'duration', 'value' => 60],
            ['label' => 'Durata 90 minute', 'type' => 'duration', 'value' => 90],
            ['label' => 'Însoțit la Poliție', 'type' => 'array', 'keys' => ['accompanied_to'], 'value' => 'politie'],
            ['label' => 'Însoțit la Avocat', 'type' => 'array', 'keys' => ['accompanied_to'], 'value' => 'avocat'],
            ['label' => 'Însoțit la Instanță', 'type' => 'array', 'keys' => ['accompanied_to'], 'value' => 'instanta'],
            ['label' => 'Demers inițiat - Ordin de protecție', 'type' => 'array', 'keys' => ['legal_actions_initiated'], 'value' => 'ordin_protectie'],
            ['label' => 'Demers soluționat/retras - Ordin de protecție', 'type' => 'array', 'keys' => ['legal_actions_closed'], 'value' => 'ordin_protectie'],
        ];

        return array_map(function (array $row) use ($sessionSlots): array {
            $values = [];

            foreach ($sessionSlots as $slot) {
                $values[] = match ($row['type']) {
                    'text' => $this->formatter->toPrintableValue(data_get($slot, $row['key'])),
                    'duration' => (int) data_get($slot, 'duration') === $row['value'] ? 'X' : '',
                    'bool' => in_array((string) data_get($slot, $row['keys'][0]), [(string) $row['value']], true)
                        || in_array((string) data_get($slot, $row['keys'][1] ?? ''), [(string) $row['value']], true) ? 'X' : '',
                    'array' => in_array((string) $row['value'], collect(data_get($slot, $row['keys'][0], []))->map(static fn ($v): string => (string) $v)->all(), true) ? 'X' : '',
                    default => '',
                };
            }

            return [
                'label' => $row['label'],
                'values' => $values,
            ];
        }, $rows);
    }

    /**
     * @param  array<string, mixed>  $sheetData
     * @param  list<array<string, mixed>>  $sessionSlots
     * @return list<array{details:string,session_number:string,schedule_date:string,schedule_time:string}>
     */
    private function extractDetailsRows(array $sheetData, array $sessionSlots): array
    {
        $raw = data_get($sheetData, 'section_4_details', data_get($sheetData, 'session_details', []));
        $rows = is_array($raw) ? array_values($raw) : [];

        if ($rows === []) {
            $rows = array_map(
                fn (array $slot, int $index): array => $this->detailRowFromSessionSlot($slot, $index),
                array_slice($sessionSlots, 0, 5),
                array_keys(array_slice($sessionSlots, 0, 5))
            );
        } else {
            $rows = array_map(
                fn ($row, int $index): array => $this->mergeDetailRowWithSessionSlot(
                    $this->parseDetailRowFromSheet($row, $index),
                    $sessionSlots[$index] ?? []
                ),
                $rows,
                array_keys($rows)
            );
        }

        $rows = array_map(fn (array $row): array => $this->finalizeDetailRowForPdf($row), $rows);

        $rows = array_slice($rows, 0, 5);
        while (count($rows) < 5) {
            $rows[] = [
                'details' => '—',
                'session_number' => '—',
                'schedule_date' => '—',
                'schedule_time' => '—',
            ];
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $slot
     * @return array{details:string,session_number:string,schedule_date:string,schedule_time:string}
     */
    private function detailRowFromSessionSlot(array $slot, int $index): array
    {
        $details = trim((string) ($slot['details'] ?? $slot['observations'] ?? $slot['topic'] ?? ''));

        return [
            'details' => $details,
            'session_number' => (string) ($slot['session_number'] ?? ($index + 1)),
            'schedule_date' => $this->formatScheduleDate($this->pickFirstPresent($slot, [
                'date', 'next_date', 'schedule_date', 'programare_data', 'programming_date',
            ])),
            'schedule_time' => $this->formatScheduleTime($this->pickFirstPresent($slot, [
                'time', 'next_time', 'schedule_time', 'programare_ora', 'programming_time',
            ])),
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{details:string,session_number:string,schedule_date:string,schedule_time:string}
     */
    private function parseDetailRowFromSheet(mixed $row, int $index): array
    {
        if (! is_array($row)) {
            return [
                'details' => trim((string) $row),
                'session_number' => (string) ($index + 1),
                'schedule_date' => '',
                'schedule_time' => '',
            ];
        }

        $details = trim((string) ($row['details'] ?? $row['detalii'] ?? $row['observations'] ?? ''));
        $sessionNumber = $row['session_number'] ?? $row['numar_sedinta'] ?? ($index + 1);

        $dateRaw = $this->pickFirstPresent($row, [
            'schedule_date', 'programare_data', 'programming_date', 'next_date', 'date', 'data',
        ]);
        if ($dateRaw === null || (is_string($dateRaw) && trim($dateRaw) === '')) {
            $dateRaw = data_get($row, 'programare.data')
                ?? data_get($row, 'programare.date')
                ?? data_get($row, 'scheduling.date')
                ?? data_get($row, 'appointment.date');
        }

        $timeRaw = $this->pickFirstPresent($row, [
            'schedule_time', 'programare_ora', 'programming_time', 'next_time', 'time', 'ora',
        ]);
        if ($timeRaw === null) {
            $timeRaw = data_get($row, 'programare.time')
                ?? data_get($row, 'programare.ora')
                ?? data_get($row, 'scheduling.time')
                ?? data_get($row, 'appointment.time');
        }

        return [
            'details' => $details,
            'session_number' => is_scalar($sessionNumber) ? (string) $sessionNumber : (string) ($index + 1),
            'schedule_date' => $this->formatScheduleDate($dateRaw),
            'schedule_time' => $this->formatScheduleTime($timeRaw),
        ];
    }

    /**
     * @param  array{details:string,session_number:string,schedule_date:string,schedule_time:string}  $row
     * @param  array<string, mixed>  $slot
     * @return array{details:string,session_number:string,schedule_date:string,schedule_time:string}
     */
    private function mergeDetailRowWithSessionSlot(array $row, array $slot): array
    {
        if ($slot === []) {
            return $row;
        }

        if ($row['details'] === '') {
            $row['details'] = trim((string) ($slot['details'] ?? $slot['observations'] ?? $slot['topic'] ?? ''));
        }

        if ($row['session_number'] === '') {
            $fallbackNum = $slot['session_number'] ?? null;
            if ($fallbackNum !== null && $fallbackNum !== '') {
                $row['session_number'] = (string) $fallbackNum;
            }
        }

        if ($row['schedule_date'] === '') {
            $row['schedule_date'] = $this->formatScheduleDate($this->pickFirstPresent($slot, [
                'date', 'next_date', 'schedule_date', 'programare_data', 'programming_date',
            ]));
        }

        if ($row['schedule_time'] === '') {
            $row['schedule_time'] = $this->formatScheduleTime($this->pickFirstPresent($slot, [
                'time', 'next_time', 'schedule_time', 'programare_ora', 'programming_time',
            ]));
        }

        return $row;
    }

    /**
     * @param  array{details:string,session_number:string,schedule_date:string,schedule_time:string}  $row
     * @return array{details:string,session_number:string,schedule_date:string,schedule_time:string}
     */
    private function finalizeDetailRowForPdf(array $row): array
    {
        return [
            'details' => $row['details'] !== '' ? $this->formatter->toPrintableValue($row['details']) : '—',
            'session_number' => $row['session_number'] !== '' ? $this->formatter->toPrintableValue($row['session_number']) : '—',
            'schedule_date' => $row['schedule_date'] !== '' ? $this->formatter->toPrintableValue($row['schedule_date']) : '—',
            'schedule_time' => $row['schedule_time'] !== '' ? $this->formatter->toPrintableValue($row['schedule_time']) : '—',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $keys
     */
    private function pickFirstPresent(array $data, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = data_get($data, $key);
            if ($value === null) {
                continue;
            }
            if (is_string($value) && trim($value) === '') {
                continue;
            }

            return $value;
        }

        return null;
    }

    private function formatScheduleDate(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof CarbonInterface) {
            return $value->format('d.m.Y');
        }

        if (is_string($value)) {
            $trim = trim($value);
            if ($trim === '') {
                return '';
            }

            if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $trim) === 1) {
                return $trim;
            }

            try {
                return Carbon::parse($trim)->format('d.m.Y');
            } catch (\Throwable) {
                return $trim;
            }
        }

        return '';
    }

    private function formatScheduleTime(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof CarbonInterface) {
            return $value->format('H:i');
        }

        if (is_string($value)) {
            $trim = trim($value);
            if ($trim === '') {
                return '';
            }

            if (preg_match('/^\d{1,2}:\d{2}(?::\d{2})?$/', $trim) === 1) {
                try {
                    return Carbon::parse($trim)->format('H:i');
                } catch (\Throwable) {
                    return $trim;
                }
            }

            try {
                return Carbon::parse($trim)->format('H:i');
            } catch (\Throwable) {
                return $trim;
            }
        }

        return '';
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
