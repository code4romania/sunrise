<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Support;

use App\Enums\Ternary;
use BackedEnum;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use UnitEnum;

class ExportDataFormatter
{
    /**
     * @return array<int, array{label:string, value:string}>
     */
    public function normalizeArray(array $data, string $prefix = ''): array
    {
        $rows = [];

        foreach ($data as $key => $value) {
            if ($this->shouldSkipKey((string) $key)) {
                continue;
            }

            $label = trim(($prefix !== '' ? "{$prefix} / " : '').$this->humanize((string) $key));

            if (is_array($value)) {
                if ($this->isList($value)) {
                    if ($this->isScalarList($value)) {
                        $rows[] = [
                            'label' => $label,
                            'value' => collect($value)
                                ->filter(fn (mixed $item): bool => $item !== null && $item !== '')
                                ->map(fn (mixed $item): string => $this->formatValueForKey((string) $key, $item))
                                ->implode(', '),
                        ];

                        continue;
                    }

                    foreach ($value as $index => $item) {
                        if (is_array($item)) {
                            $rows = array_merge($rows, $this->normalizeArray($item, "{$label} #".($index + 1)));

                            continue;
                        }

                        $rows[] = [
                            'label' => "{$label} #".($index + 1),
                            'value' => $this->formatValueForKey((string) $key, $item),
                        ];
                    }

                    continue;
                }

                $rows = array_merge($rows, $this->normalizeArray($value, $label));

                continue;
            }

            $rows[] = [
                'label' => $label,
                'value' => $this->formatValueForKey((string) $key, $value),
            ];
        }

        return $rows;
    }

    public function toPrintableValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if ($value instanceof CarbonInterface) {
            return $value->format('d.m.Y');
        }

        if ($value instanceof BackedEnum) {
            if (method_exists($value, 'getLabel')) {
                return (string) $value->getLabel();
            }

            return (string) $value->value;
        }

        if ($value instanceof UnitEnum) {
            if (method_exists($value, 'getLabel')) {
                return (string) $value->getLabel();
            }

            return $value->name;
        }

        if ($value instanceof Collection) {
            return $value->map(fn (mixed $item): string => $this->toPrintableValue($item))->implode(', ');
        }

        if (is_string($value) && $this->looksLikeDate($value)) {
            try {
                return Carbon::parse($value)->format('d.m.Y');
            } catch (\Throwable) {
                return $value;
            }
        }

        if (is_string($value)) {
            $translated = $this->translateEnumLikeValue($value);
            if ($translated !== null) {
                return $translated;
            }
        }

        if (is_bool($value)) {
            return $value ? __('general.yes') : __('general.no');
        }

        if (is_array($value)) {
            return collect($value)->map(fn (mixed $item): string => $this->toPrintableValue($item))->implode(', ');
        }

        return strip_tags((string) $value);
    }

    private function humanize(string $key): string
    {
        $translated = $this->translateLabel($key);
        if ($translated !== null) {
            return $translated;
        }

        return ucfirst(str_replace('_', ' ', $key));
    }

    private function translateLabel(string $key): ?string
    {
        $inlineMap = [
            'aggressors' => 'Agresori',
            'details' => 'Detalii',
            'antecedents' => 'Antecedente',
            'flow_presentation' => 'Flux prezentare',
            'name' => 'Nume',
            'status' => 'Status',
            'violence' => 'Violență',
            'risk_factors' => 'Factori de risc',
            'requested_services' => 'Servicii solicitate',
            'beneficiary_situation' => 'Situația beneficiarului',
            'value' => 'Valoare',
            'description' => 'Descriere',
            'role_for_display' => 'Rol',
            'case_permissions' => 'Permisiuni caz',
            'ngo_admin_permissions' => 'Permisiuni admin ONG',
            'manager_case' => 'Manager de caz',
            'monthly_plans' => 'Planuri lunare',
            'monthly_plan_services' => 'Servicii plan lunar',
            'monthly_plan_interventions' => 'Intervenții plan lunar',
            'service_intervention' => 'Intervenție',
            'start_date' => 'Data de început',
            'end_date' => 'Data de final',
            'case_manager_user_id' => 'Manager de caz',
            'specialists' => 'Echipă caz',
            'objections' => 'Obiective',
            'expected_results' => 'Rezultate așteptate',
            'procedure' => 'Procedura',
            'indicators' => 'Indicatori',
            'achievement_degree' => 'Grad de realizare',
            'observations' => 'Observații',
            'health_insurance' => 'Asigurare de sănătate',
            'observations_chronic_diseases' => 'Observații boli cronice',
            'observations_mental_illness' => 'Observații boli psihice',
            'disabilities' => 'Dizabilități',
            'type_of_disability' => 'Tip dizabilitate',
            'degree_of_disability' => 'Grad dizabilitate',
            'observations_disability' => 'Observații dizabilitate',
            'income_source' => 'Sursa venitului',
            'drug_consumption' => 'Consum de substanțe',
            'drug_types' => 'Tipuri de substanțe',
            'other_current_medication' => 'Altă medicație curentă',
        ];

        if (array_key_exists($key, $inlineMap)) {
            return $inlineMap[$key];
        }

        $candidates = [
            "field.{$key}",
            "field.aggressor_{$key}",
            "monitoring.labels.{$key}",
            "intervention_plan.labels.{$key}",
            "beneficiary.section.initial_evaluation.labels.{$key}",
            "beneficiary.section.close_file.labels.{$key}",
            "beneficiary.section.detailed_evaluation.labels.{$key}",
            "beneficiary.labels.{$key}",
            "case.view.{$key}",
        ];

        foreach ($candidates as $candidate) {
            if (Lang::has($candidate)) {
                $value = __($candidate);
                if ($value !== $candidate) {
                    return $value;
                }
            }
        }

        return null;
    }

    private function looksLikeDate(string $value): bool
    {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}(?:[T\s].*)?$/', $value);
    }

    private function translateEnumLikeValue(string $value): ?string
    {
        $normalized = strtolower(trim($value));
        if (! preg_match('/^[a-z0-9_]+$/', $normalized)) {
            return null;
        }

        $candidates = [
            "enum.helps.{$normalized}",
            "enum.level.{$normalized}",
            "enum.frequency.{$normalized}",
            "enum.ternary.{$normalized}",
            "enum.meeting_status.{$normalized}",
            "enum.role.{$normalized}",
            "enum.gender.{$normalized}",
            "enum.gender_short_values.{$normalized}",
            "enum.civil_status.{$normalized}",
            "enum.id_type.{$normalized}",
            "enum.studies.{$normalized}",
            "enum.occupation.{$normalized}",
            "enum.income.{$normalized}",
            "enum.homeownership.{$normalized}",
            "enum.diseases.{$normalized}",
            "enum.citizenship.{$normalized}",
            "enum.drug.{$normalized}",
            "enum.case_permissions.{$normalized}",
            "enum.admin_permission.{$normalized}",
            "enum.violence_means.{$normalized}",
            "enum.violence.{$normalized}",
            "enum.protection_order.{$normalized}",
            "enum.referral_mode.{$normalized}",
            "enum.presentation_mode.{$normalized}",
            "enum.aggressor_relationship.{$normalized}",
            "enum.aggressor_legal_history.{$normalized}",
            "enum.maintenance_sources.{$normalized}",
            "enum.child_aggressor_relationships.{$normalized}",
            "beneficiary.section.initial_evaluation.labels.{$normalized}",
            "beneficiary.section.detailed_evaluation.labels.{$normalized}",
            "intervention_plan.labels.{$normalized}",
        ];

        foreach ($candidates as $candidate) {
            if (Lang::has($candidate)) {
                $translated = __($candidate);
                if ($translated !== $candidate) {
                    return $translated;
                }
            }
        }

        return null;
    }

    private function formatValueForKey(string $key, mixed $value): string
    {
        if ($key === 'value' && in_array($value, [-1, 0, 1, '-1', '0', '1'], true)) {
            return Ternary::from((int) $value)->getLabel();
        }

        return $this->toPrintableValue($value);
    }

    private function isList(array $array): bool
    {
        if ($array === []) {
            return true;
        }

        return array_keys($array) === range(0, count($array) - 1);
    }

    private function shouldSkipKey(string $key): bool
    {
        if (in_array($key, ['id', 'ulid', 'created_at', 'updated_at', 'deleted_at', 'pivot'], true)) {
            return true;
        }

        if (str_ends_with($key, '_id')) {
            return true;
        }

        if (str_starts_with($key, 'role_for_display') || str_starts_with($key, 'service_without_status_condition')) {
            return true;
        }

        return false;
    }

    private function isScalarList(array $array): bool
    {
        foreach ($array as $item) {
            if (is_array($item) || $item instanceof Collection) {
                return false;
            }
        }

        return true;
    }
}
