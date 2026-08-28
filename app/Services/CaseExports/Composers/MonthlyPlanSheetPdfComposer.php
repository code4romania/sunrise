<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Composers;

use App\Models\BenefitService;
use App\Models\MonthlyPlan;
use App\Models\MonthlyPlanInterventions;
use App\Models\MonthlyPlanService;
use App\Models\Specialist;
use App\Services\CaseExports\Support\ExportDataFormatter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

class MonthlyPlanSheetPdfComposer
{
    /**
     * @var list<string>
     */
    private const SERVICE_TABLE_IDENTIFIERS = ['GPS', 'MED', 'SOC', 'PSI', 'EDU', 'JRD', 'VOF', 'MAT'];

    /**
     * @var list<string>
     */
    private const INTERVENTION_TABLE_IDENTIFIERS = ['GPS', 'MED', 'SOC', 'PSI', 'EDU', 'JRD', 'AMPS', 'VOF', 'MAT'];

    public function __construct(
        private readonly ExportDataFormatter $formatter,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function compose(MonthlyPlan $monthlyPlan): array
    {
        $monthlyPlan->loadMissing([
            'interventionPlan.beneficiary',
            'interventionPlan.benefits.benefit',
            'monthlyPlanServices.service',
            'monthlyPlanServices.monthlyPlanInterventions.serviceIntervention.service',
            'caseManager',
        ]);

        $interventionPlan = $monthlyPlan->interventionPlan;
        $beneficiary = $interventionPlan?->beneficiary;

        $groupedServices = $this->groupMonthlyPlanServices($monthlyPlan);

        $benefitRows = [];
        if ($interventionPlan !== null) {
            foreach ($interventionPlan->benefits as $index => $benefitService) {
                if ($benefitService instanceof BenefitService) {
                    $benefitRows[] = $this->buildBenefitRow($index + 1, $benefitService);
                }
            }
        }

        $accordedCodes = $this->orderedAccordedServiceCodes($groupedServices);

        $serviceRows = [];
        foreach ($accordedCodes as $identifier) {
            $rows = $groupedServices->get($identifier, collect());
            if ($rows->isEmpty()) {
                continue;
            }
            $serviceRows[] = $this->buildServiceSheetRow(
                $identifier,
                $rows,
                $identifier === 'GPS',
            );
        }

        $interventionRows = [];
        foreach ($accordedCodes as $identifier) {
            $rows = $groupedServices->get($identifier, collect());
            if ($rows->isEmpty()) {
                continue;
            }
            $interventionRow = $this->buildInterventionSheetRow($identifier, $rows);
            if ($this->interventionRowHasOnlyPlaceholderCells($interventionRow)) {
                continue;
            }
            $interventionRows[] = $interventionRow;
        }

        $sheetDate = $this->formatter->toPrintableValue($interventionPlan?->plan_date ?? $monthlyPlan->start_date);
        $period = $monthlyPlan->interval;

        return [
            'beneficiary_name' => $beneficiary !== null && trim((string) $beneficiary->full_name) !== ''
                ? (string) $beneficiary->full_name
                : '—',
            'sheet_date' => $sheetDate,
            'plan_period' => $period,
            'benefit_rows' => $benefitRows,
            'service_rows' => $serviceRows,
            'intervention_rows' => $interventionRows,
            'team_rows' => $this->buildTeamRows($monthlyPlan),
            'beneficiary_sign_name' => $beneficiary !== null && trim((string) $beneficiary->full_name) !== ''
                ? (string) $beneficiary->full_name
                : '—',
        ];
    }

    /**
     * @return Collection<string, Collection<int, MonthlyPlanService>>
     */
    private function groupMonthlyPlanServices(MonthlyPlan $monthlyPlan): Collection
    {
        /** @var Collection<string, Collection<int, MonthlyPlanService>> $grouped */
        $grouped = collect();

        foreach ($monthlyPlan->monthlyPlanServices as $row) {
            if (! $row instanceof MonthlyPlanService) {
                continue;
            }

            $code = $this->resolveCanonicalServiceCode($row);
            if ($code === null) {
                continue;
            }

            if (! $grouped->has($code)) {
                $grouped->put($code, collect());
            }

            $grouped->get($code)->push($row);
        }

        return $grouped;
    }

    /**
     * Stable order: known template codes first (when present), then any other grouped keys.
     *
     * @return list<string>
     */
    private function orderedAccordedServiceCodes(Collection $grouped): array
    {
        $codes = [];
        foreach (self::SERVICE_TABLE_IDENTIFIERS as $id) {
            if ($grouped->get($id)?->isNotEmpty() ?? false) {
                $codes[] = $id;
            }
        }

        $remaining = $grouped->keys()
            ->filter(static fn (mixed $key): bool => ! in_array((string) $key, self::SERVICE_TABLE_IDENTIFIERS, true))
            ->sort()
            ->values()
            ->map(static fn (mixed $key): string => (string) $key)
            ->all();

        foreach ($remaining as $id) {
            if ($grouped->get($id)?->isNotEmpty() ?? false) {
                $codes[] = $id;
            }
        }

        return $codes;
    }

    /**
     * @param  array<string, string>  $row
     */
    private function interventionRowHasOnlyPlaceholderCells(array $row): bool
    {
        return ($row['objectives'] ?? '—') === '—'
            && ($row['observations'] ?? '—') === '—';
    }

    private function resolveServiceRowLabel(string $identifier, Collection $rows): string
    {
        $labelKey = 'intervention_plan.sheet.service_row.'.$identifier;
        if (Lang::has($labelKey)) {
            return __($labelKey);
        }

        $first = $rows->first();
        if ($first instanceof MonthlyPlanService && $first->service?->name !== null && trim((string) $first->service->name) !== '') {
            return (string) $first->service->name;
        }

        return $identifier;
    }

    private function resolveInterventionRowLabel(string $identifier, Collection $rows): string
    {
        $labelKey = 'intervention_plan.sheet.intervention_row.'.$identifier;
        if (Lang::has($labelKey)) {
            return __($labelKey);
        }

        $first = $rows->first();
        if ($first instanceof MonthlyPlanService && $first->service?->name !== null && trim((string) $first->service->name) !== '') {
            return (string) $first->service->name;
        }

        return $identifier;
    }

    /**
     * @return list<string>
     */
    private function allowedCanonicalCodes(): array
    {
        return array_values(array_unique(array_merge(
            self::SERVICE_TABLE_IDENTIFIERS,
            self::INTERVENTION_TABLE_IDENTIFIERS,
        )));
    }

    private function resolveCanonicalServiceCode(MonthlyPlanService $row): ?string
    {
        $allowed = $this->allowedCanonicalCodes();

        $direct = strtoupper(trim((string) ($row->service?->identifier ?? '')));
        if ($direct !== '' && in_array($direct, $allowed, true)) {
            return $direct;
        }

        foreach ($row->monthlyPlanInterventions as $intervention) {
            if (! $intervention instanceof MonthlyPlanInterventions) {
                continue;
            }

            $svcIdentifier = strtoupper(trim((string) ($intervention->serviceIntervention?->service?->identifier ?? '')));
            if ($svcIdentifier !== '' && in_array($svcIdentifier, $allowed, true)) {
                return $svcIdentifier;
            }

            $intIdentifier = (string) ($intervention->serviceIntervention?->identifier ?? '');
            if ($intIdentifier !== '' && preg_match('/^([A-Za-z]+)_/', $intIdentifier, $matches)) {
                $prefix = strtoupper($matches[1]);
                if (in_array($prefix, $allowed, true)) {
                    return $prefix;
                }
            }
        }

        $serviceName = trim((string) ($row->service?->name ?? ''));
        if ($serviceName !== '') {
            foreach (self::SERVICE_TABLE_IDENTIFIERS as $code) {
                $label = (string) __('intervention_plan.sheet.service_row.'.$code);
                if ($label !== '' && $this->namesLooselyMatchService($serviceName, $label)) {
                    return $code;
                }
            }

            $ampsHints = [
                (string) __('intervention_plan.sheet.intervention_row.AMPS'),
                'Alte masuri pentru protecție și siguranță',
                'Alte măsuri pentru protecție și siguranță',
            ];
            foreach ($ampsHints as $hint) {
                if ($hint !== '' && $this->namesLooselyMatchService($serviceName, $hint)) {
                    return 'AMPS';
                }
            }
        }

        return null;
    }

    private function namesLooselyMatchService(string $serviceName, string $referenceLabel): bool
    {
        $a = $this->normalizeComparableName($serviceName);
        $b = $this->normalizeComparableName($referenceLabel);
        if ($a === '' || $b === '') {
            return false;
        }

        if ($a === $b) {
            return true;
        }

        if (str_contains($a, $b) || str_contains($b, $a)) {
            return true;
        }

        similar_text($a, $b, $percent);

        return $percent >= 88.0;
    }

    private function normalizeComparableName(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
        $value = str_replace(['ș', 'ş'], 's', $value);
        $value = str_replace(['ț', 'ţ'], 't', $value);
        $value = str_replace(['ă', 'â'], 'a', $value);
        $value = str_replace('î', 'i', $value);

        return trim((string) preg_replace('/[^\p{L}\p{N}\s]+/u', '', $value));
    }

    /**
     * @return array<string, string|int>
     */
    private function buildBenefitRow(int $nr, BenefitService $benefitService): array
    {
        $types = $benefitService->benefit_types;
        $typeLabel = '—';
        if (is_array($types) && $types !== []) {
            $typeLabel = collect($types)->map(fn (mixed $t): string => $this->formatter->toPrintableValue($t))->implode(', ');
        }

        $benefitName = $benefitService->benefit?->name ?? '—';
        $typeDisplay = $typeLabel !== '—' ? $benefitName.' — '.$typeLabel : $benefitName;

        return [
            'nr' => $nr,
            'type' => $typeDisplay,
            'amount' => $this->formatter->toPrintableValue($benefitService->description),
            'authority' => __('intervention_plan.sheet.default_authority'),
            'start' => '—',
            'period' => '—',
        ];
    }

    /**
     * @param  Collection<int, MonthlyPlanService>  $rows
     * @return array<string, mixed>
     */
    private function buildServiceSheetRow(string $identifier, Collection $rows, bool $isGps): array
    {
        if ($rows->isEmpty()) {
            return [
                'identifier' => $identifier,
                'label' => $this->resolveServiceRowLabel($identifier, $rows),
                'institution' => '—',
                'objectives' => '—',
                'start' => '—',
                'period' => '—',
                'responsible' => '—',
                'is_gps' => $isGps,
                'admission_checked' => false,
                'continuation_checked' => false,
            ];
        }

        $institution = $this->firstNonEmptyPrintable($rows, 'institution');
        $responsible = $this->firstNonEmptyPrintable($rows, 'responsible_person');
        $objectives = $this->mergeServiceObjectivesAndDetails($rows);

        $startDates = $rows->map(fn (MonthlyPlanService $r): mixed => $r->start_date)->filter();
        $endDates = $rows->map(fn (MonthlyPlanService $r): mixed => $r->end_date)->filter();

        $start = $startDates->isNotEmpty()
            ? $this->formatter->toPrintableValue($startDates->sort()->first())
            : '—';

        $period = '—';
        if ($startDates->isNotEmpty() && $endDates->isNotEmpty()) {
            $minStart = $startDates->sort()->first();
            $maxEnd = $endDates->sort()->last();
            $period = trim(
                $this->formatter->toPrintableValue($minStart).' – '.$this->formatter->toPrintableValue($maxEnd)
            );
        } elseif ($rows->isNotEmpty()) {
            $first = $rows->first();
            if ($first instanceof MonthlyPlanService) {
                $period = trim(
                    $this->formatter->toPrintableValue($first->start_date).' – '.$this->formatter->toPrintableValue($first->end_date)
                );
            }
        }

        $admission = $rows->contains(fn (MonthlyPlanService $r): bool => $this->gpsOptionChecked($r, ['GPS_10', 'GPS_11', 'admitere']));
        $continuation = $rows->contains(fn (MonthlyPlanService $r): bool => $this->gpsOptionChecked($r, ['continuare', 'Continuare']));

        return [
            'identifier' => $identifier,
            'label' => $this->resolveServiceRowLabel($identifier, $rows),
            'institution' => $institution,
            'objectives' => $objectives,
            'start' => $start,
            'period' => $period,
            'responsible' => $responsible,
            'is_gps' => $isGps,
            'admission_checked' => $admission,
            'continuation_checked' => $continuation,
        ];
    }

    /**
     * @param  Collection<int, MonthlyPlanService>  $rows
     */
    private function mergeServiceObjectivesAndDetails(Collection $rows): string
    {
        $parts = [];
        foreach ($rows as $row) {
            if (! $row instanceof MonthlyPlanService) {
                continue;
            }
            foreach ([$row->objective, $row->service_details] as $text) {
                if ($text !== null && trim((string) $text) !== '') {
                    $parts[] = trim((string) $text);
                }
            }
        }

        if ($parts === []) {
            return '—';
        }

        return implode("\n\n", array_values(array_unique($parts)));
    }

    /**
     * @param  Collection<int, MonthlyPlanService>  $rows
     */
    private function firstNonEmptyPrintable(Collection $rows, string $attribute): string
    {
        foreach ($rows as $row) {
            if (! $row instanceof MonthlyPlanService) {
                continue;
            }
            $raw = $row->getAttribute($attribute);
            if ($raw !== null && trim((string) $raw) !== '') {
                return $this->formatter->toPrintableValue($raw);
            }
        }

        return '—';
    }

    /**
     * @param  list<string>  $haystackHints
     */
    private function gpsOptionChecked(?MonthlyPlanService $row, array $haystackHints): bool
    {
        if ($row === null) {
            return false;
        }

        $blob = strtolower(
            (string) $row->objective
            .' '.(string) $row->service_details
        );

        foreach ($row->monthlyPlanInterventions as $intervention) {
            $blob .= ' '.strtolower((string) $intervention->serviceIntervention?->identifier);
            $blob .= ' '.strtolower((string) $intervention->serviceIntervention?->name);
            $blob .= ' '.strtolower((string) $intervention->objections);
            $blob .= ' '.strtolower((string) $intervention->procedure);
        }

        foreach ($haystackHints as $hint) {
            if ($hint !== '' && str_contains($blob, strtolower($hint))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Collection<int, MonthlyPlanService>  $rows
     * @return array<string, string>
     */
    private function buildInterventionSheetRow(string $identifier, Collection $rows): array
    {
        if ($rows->isEmpty()) {
            return [
                'label' => $this->resolveInterventionRowLabel($identifier, $rows),
                'objectives' => '—',
                'observations' => '—',
            ];
        }

        $objectiveParts = [];
        $observationParts = [];

        foreach ($rows as $row) {
            if (! $row instanceof MonthlyPlanService) {
                continue;
            }

            foreach ($row->monthlyPlanInterventions as $intervention) {
                if (! $intervention instanceof MonthlyPlanInterventions) {
                    continue;
                }

                $name = (string) ($intervention->serviceIntervention?->name ?? '');
                $block = array_filter([
                    $name !== '' ? $name : null,
                    $intervention->objections,
                    $intervention->expected_results,
                    $intervention->procedure,
                    $intervention->indicators,
                    $intervention->achievement_degree,
                ], fn (?string $p): bool => $p !== null && trim($p) !== '');

                if ($block !== []) {
                    $objectiveParts[] = implode("\n", $block);
                }

                if ($intervention->observations !== null && trim((string) $intervention->observations) !== '') {
                    $observationParts[] = trim((string) $intervention->observations);
                }
            }
        }

        return [
            'label' => $this->resolveInterventionRowLabel($identifier, $rows),
            'objectives' => $objectiveParts !== [] ? implode("\n\n", $objectiveParts) : '—',
            'observations' => $observationParts !== [] ? implode("\n\n", $observationParts) : '—',
        ];
    }

    /**
     * @return list<array{role: string, name: string}>
     */
    private function buildTeamRows(MonthlyPlan $monthlyPlan): array
    {
        $ids = $this->monthlyPlanSpecialistIds($monthlyPlan);
        if ($ids === []) {
            return [];
        }

        $specialists = Specialist::query()
            ->whereIn('id', $ids)
            ->with(['user', 'roleForDisplay'])
            ->get();

        $rows = [];
        foreach ($ids as $id) {
            $specialist = $specialists->firstWhere('id', $id);
            if ($specialist === null) {
                continue;
            }

            $rows[] = [
                'role' => (string) ($specialist->roleForDisplay?->name ?? '—'),
                'name' => (string) ($specialist->user?->full_name ?? '—'),
            ];
        }

        return $rows;
    }

    /**
     * @return list<int>
     */
    private function monthlyPlanSpecialistIds(MonthlyPlan $record): array
    {
        $fromCast = $record->specialists;
        if ($fromCast instanceof Collection && $fromCast->isNotEmpty()) {
            return $this->normalizeSpecialistIdList($fromCast->all());
        }

        $raw = $record->getRawOriginal('specialists');
        if ($raw === null || $raw === '' || ! is_string($raw)) {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $this->normalizeSpecialistIdList($decoded);
        }

        return $this->normalizeSpecialistIdList(array_map('trim', explode(',', $raw)));
    }

    /**
     * @param  array<mixed>  $values
     * @return list<int>
     */
    private function normalizeSpecialistIdList(array $values): array
    {
        $ids = [];
        foreach ($values as $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $ids[] = (int) $value;
        }

        /** @var list<int> $unique */
        $unique = array_values(array_unique(array_filter($ids, static fn (int $id): bool => $id > 0)));

        return $unique;
    }
}
