<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryHistoryResource\Pages;

use App\Concerns\HasParentResource;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryHistoryResource;
use App\Infolists\Components\HistoryLine;
use App\Models\Activity;
use App\Models\City;
use App\Models\County;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;

class ViewBeneficiaryHistories extends ViewRecord
{
    use HasParentResource;

    protected static string $resource = BeneficiaryHistoryResource::class;

    protected string $relationshipKey = 'subject_id';

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)
            ->getHistoryBreadcrumbs();
    }

    public function getTitle(): string|Htmlable
    {
        $record = $this->getRecord();
        $state = $record->subject_type;

        return $state === 'beneficiary' ? ucfirst($state) : 'Beneficiary, ' . ucfirst($state);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(function (Activity $record) {
            return $this->getActivitySchema($record);
        });
    }

    private function getActivitySchema(Activity $record)
    {
        $schema = [];

        $newValues = $record->properties->get('attributes');
        if ($newValues) {
            foreach ($newValues as $field => $value) {
                $oldValue = $record->properties->get('old')[$field] ?? null;
                $schema = array_merge($schema, $this->getFieldSchema($record, $field, $value, $oldValue));
            }
        }

        $oldValues = $record->properties->get('old');

        if ($oldValues) {
            foreach ($oldValues as $field => $value) {
                if (isset($newValues[$field])) {
                    continue;
                }
                $newValue = $record->properties->get('attributes')[$field] ?? null;
                $schema = array_merge($schema, $this->getFieldSchema($record, $field, $newValue, $value));
            }
        }

        return [
            Section::make()
                ->columns()
                ->maxWidth('3xl')
                ->schema($schema),

        ];
    }

    private function getFieldSchema(Activity $record, string $field, $newValue, $oldValue): array
    {
        if ($field === 'beneficiary_id' || $field === 'organization_id') {
            return [];
        }

        if (blank($oldValue) && blank($newValue)) {
            return [];
        }

        $castType = $record->subject->getCasts()[$field] ?? null;

        if ($castType) {
            if ($castType === 'json') {
                if ($field === 'risk_factors') {
                    return $this->getSchemaForRiskFactors($newValue, $oldValue);
                }
            }

            if ($castType === 'collection') {
                return $this->getSchemaForCollection($newValue, $oldValue, $field);
            }

            $oldValue = $this->convertToEnum($castType, $oldValue);
            $newValue = $this->convertToEnum($castType, $newValue);
        }

        if (str_contains($field, 'city_id')) {
            $oldValue = (int) $oldValue ? $this->convertToCity($oldValue) : '-';
            $newValue = (int) $newValue ? $this->convertToCity($newValue) : '-';
        }

        if (str_contains($field, 'county_id')) {
            $oldValue = (int) $oldValue ? $this->convertToCounty($oldValue) : '-';
            $newValue = (int) $newValue ? $this->convertToCounty($newValue) : '-';
        }

        return $this->getSchema($field, $oldValue, $newValue);
    }

    private function getFieldLabel(string $field): string
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

        $filedWithoutID = str_replace('_id', '', $field);
        foreach ($translatePaths as $path) {
            $fieldPath = implode('.', [$path, $field]);

            if (__($fieldPath) !== $fieldPath) {
                return __($fieldPath);
            }

            $fieldPathWithoutID = implode('.', [$path, $filedWithoutID]);

            if (__($fieldPathWithoutID) !== $fieldPathWithoutID) {
                return __($fieldPathWithoutID);
            }
        }

        return $field;
    }

    /**
     * @param  mixed $castType
     * @param        $fieldValue
     * @return array
     */
    public function convertToEnum(mixed $castType, mixed $fieldValue): mixed
    {
        if (enum_exists($castType)) {
            $fieldValue = ! blank($fieldValue) ? $castType::tryFrom($fieldValue) : '-';
        }

        if (str_contains($castType, AsEnumCollection::class)) {
            $castType = str_replace(AsEnumCollection::class . ':', '', $castType);
            if (enum_exists($castType)) {
                if ($fieldValue) {
                    foreach ($fieldValue as &$value) {
                        $value = ! blank($value) ? $castType::tryFrom($value) : '-';
                    }
                }
            }
        }

        return $fieldValue;
    }

    /**
     * @param  string $field
     * @param  mixed  $oldValue
     * @param  mixed  $newValue
     * @return array
     */
    public function getSchema(string $field, mixed $oldValue, mixed $newValue, mixed $oldDescription = null, mixed $newDescription = null): array
    {
        return [
            HistoryLine::make($field)
                ->oldValue($oldValue)
                ->newValue($newValue)
                ->oldDescription($oldDescription)
                ->newDescription($newDescription)
                ->section($this->getRecord()->subject_type),
        ];
    }

    /**
     * @param         $newValue
     * @param         $oldValue
     * @param  string $field
     * @return array
     */
    public function getSchemaForCollection($newValue, $oldValue, string $field): array
    {
        $sections = [];

        foreach ($newValue as $key => $repeaterData) {
            $schema = [];
            $oldData = $oldValue ? $oldValue[$key] : null;

            foreach ($repeaterData as $subField => $v) {
                $oldDataFiled = $oldData[$subField] ?? null;
                if ($v == $oldDataFiled) {
                    continue;
                }

                if (blank($v) && blank($oldDataFiled)) {
                    continue;
                }

                $schema = array_merge($schema, $this->getSchema($subField, $oldDataFiled, $v));
            }

            if ($oldData) {
                foreach ($oldData as $subField => $v) {
                    if (isset($repeaterData[$subField]) || blank($v)) {
                        continue;
                    }

                    $schema = array_merge($schema, $this->getSchema($subField, $v, $repeaterData[$subField]));
                }
            }
            if (empty($schema)) {
                continue;
            }

            $sections[] = Section::make($this->getFieldLabel($field))
                ->columns()
                ->schema($schema);
        }

        return $sections;
    }

    /**
     * @param        $newValue
     * @param        $oldValue
     * @return array
     */
    public function getSchemaForRiskFactors($newValue, $oldValue): array
    {
        $schema = [];

        foreach ($newValue as $key => $newData) {
            $oldData = $oldValue ? $oldValue[$key] : null;

            $oldFieldValue = $oldData['value'] ?? null;
            $oldFieldValue = $oldFieldValue ? Ternary::tryFrom($oldFieldValue) : null;
            $newFieldValue = $newData['value'] ?? null;
            $newFieldValue = $newFieldValue ? Ternary::tryFrom($newFieldValue) : null;

            $schema = array_merge($schema, $this->getSchema(
                $key,
                $oldFieldValue,
                $newFieldValue ?? null,
                $oldData['description'] ?? null,
                $newData['description'] ?? null
            ));
        }

        if ($oldValue) {
            foreach ($oldValue as $key => $oldData) {
                if (isset($newValue[$key]) || blank($oldData)) {
                    continue;
                }
                $newData = $newValue[$key];

                $oldFieldValue = $oldData['value'] ?? null;
                $oldFieldValue = $oldFieldValue ? Ternary::tryFrom($oldFieldValue) : null;
                $newFieldValue = $newData['value'] ?? null;
                $newFieldValue = $newFieldValue ? Ternary::tryFrom($newFieldValue) : null;

                $schema = array_merge($schema, $this->getSchema(
                    $key,
                    $oldFieldValue,
                    $newFieldValue,
                    $oldData['description'] ?? null,
                    $newData['description'] ?? null,
                ));
            }
        }

        return $schema;
    }

    private function convertToCity(int $cityId): ?string
    {
        return City::find($cityId)->name;
    }

    private function convertToCounty(int $countyId): ?string
    {
        return County::find($countyId)->name;
    }
}
