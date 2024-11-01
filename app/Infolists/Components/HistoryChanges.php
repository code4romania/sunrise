<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use App\Enums\Ternary;
use App\Models\City;
use App\Models\County;
use App\Models\User;
use Filament\Infolists\Components\Entry;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HistoryChanges extends Entry
{
    protected string $view = 'infolists.components.history-changes';

    protected function setUp(): void
    {
        $this->columnSpanFull();
    }

    public function getFields(): Collection
    {
        $state = $this->getState();

        $old = collect($state->get('old'));
        $new = collect($state->get('attributes'));

        return $old->keys()
            ->merge($new->keys())
            ->filter(fn ($item) => $item !== 'beneficiary_id' && $item !== 'organization_id')
            ->unique();
    }

    public function getLineData(string $field, mixed $oldValue, mixed $newValue): Collection
    {
        $fieldLabel = $this->getFieldLabel($field);
        $oldValue = $this->getFieldValue($field, $oldValue);
        $newValue = $this->getFieldValue($field, $newValue);

        $subFields = [];
        if ($oldValue instanceof Collection ||
            $newValue instanceof Collection) {
            $oldValue = collect($oldValue);
            $newValue = collect($newValue);
            $subFields = $newValue->keys()
                ->merge(($oldValue)->keys())
                ->unique();
        }

        return collect([
            'label' => $fieldLabel,
            'old' => $oldValue,
            'new' => $newValue,
            'subFields' => $subFields,
        ]);
    }

    public function getFieldValue(string $field, mixed $value): mixed
    {
        $castType = $this->getCastType($field);

        if ($castType) {
            if ($castType === 'json') {
                if ($field === 'risk_factors') {
                    return $this->getRiskFactorsFields(collect($value));
                }
            }

            if ($castType === 'collection' && $value) {
                if (\is_array($value)) {
                    $value = collect($value)->map(fn ($item) => \is_array($item) ? collect($item) : $item);
                }

                return $value;
            }

            $value = $this->convertToEnum($castType, $value);
        }

        if (str_contains($field, 'city_id')) {
            $value = (int) $value ? $this->convertToCity($value) : '-';
        }

        if (str_contains($field, 'county_id')) {
            $value = (int) $value ? $this->convertToCounty($value) : '-';
        }

        if ($field === 'user_id' || $field === 'specialist_id') {
            $value = User::find((int) $value)
                ?->getFilamentName() ?? $value;
        }

        return $value;
    }

    private function getCastType(string $field): ?string
    {
        $record = $this->getRecord();

        if ($record->subject) {
            if ($record->event == $record->description->value) {
                return $record->subject->getCasts()[$field] ?? null;
            }

            if (method_exists($record->subject, $record->event)) {
                return $record->subject
                    ->{$record->event}()
                    ->getRelated()
                    ->getCasts()[$field] ?? null;
            }
        }

        $modelName = \sprintf('\App\Models\%s', ucfirst($record->event));
        $modelClass = new $modelName();

        return $modelClass->getCasts()[$field] ?? null;
    }

    private function convertToEnum(mixed $castType, mixed $fieldValue): mixed
    {
        if ($castType === 'boolean') {
            $castType = Ternary::class;
            $fieldValue = isset($fieldValue) ? (int) $fieldValue : null;
        }

        if (enum_exists($castType)) {
            $fieldValue = ! blank($fieldValue) ? $castType::tryFrom($fieldValue)?->getLabel() : '-';
        }

        if (str_contains($castType, AsEnumCollection::class)) {
            $castType = str_replace(AsEnumCollection::class . ':', '', $castType);
            if (enum_exists($castType)) {
                if ($fieldValue) {
                    foreach ($fieldValue as &$value) {
                        $value = ! blank($value) ? $castType::tryFrom($value)?->getLabel() : '-';
                    }

                    $fieldValue = collect($fieldValue)
                        ->join(', ');
                }
            }
        }

        return $fieldValue;
    }

    private function getRiskFactorsFields(Collection $values): Collection
    {
        return $values->map(function ($item) {
            $value = $item['value'] ? Ternary::tryFrom($item['value'])->getLabel() : '-';

            if ($item['description']) {
                return $value . ' (' . $item['description'] . ')';
            }

            return $value;
        });
    }

    private function convertToCity(int $cityId): ?string
    {
        return City::find($cityId)->name;
    }

    private function convertToCounty(int $countyId): ?string
    {
        return County::find($countyId)->name;
    }

    public function getFieldLabel(string $field): string
    {
        $translatePaths = [
            'field',
            'beneficiary.section.identity.labels',
            'beneficiary.section.personal_information.label',
            'beneficiary.section.initial_evaluation.labels',
            'beneficiary.section.detailed_evaluation.labels',
            'beneficiary.section.specialists.labels',
            'beneficiary.section.documents.labels',
        ];

        if ($field === 'name' &&
            $this->getRecord()->event === 'document') {
            return __('beneficiary.section.documents.labels.name');
        }

        $field = Str::replace('_id', '', $field);
        $field = $this->mapSpecialFieldName($field);

        foreach ($translatePaths as $path) {
            $key = "$path.$field";

            if (__($key) !== $key) {
                return __($key);
            }
        }

        return $field;
    }

    private function mapSpecialFieldName(string $field): string
    {
        return match ($field) {
            'name' => 'full_name',
            'legal_history' => 'aggressor_legal_history',
            'violence_types' => 'aggressor_violence_types',
            'has_drug_history' => 'aggressor_has_drug_history',
            'has_violence_history' => 'aggressor_has_violence_history',
            'has_psychiatric_history' => 'aggressor_has_psychiatric_history',
            'method_ofentifying_the_service' => 'method_of_identifying_the_service',
            default => $field,
        };
    }
}
