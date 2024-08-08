<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Concerns\RedirectToMonitoring;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Models\Monitoring;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditGeneral extends EditRecord
{
    use HasParentResource;
    use RedirectToMonitoring;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForMonitoringFileEdit($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.monitoring.titles.edit_general');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.monitoring.headings.general'));
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->maxWidth('3xl')
                ->schema(self::getSchema())]);
    }

    public static function getSchema(): array
    {
        $lastFile = self::getParent()?->monitoring->sortByDesc('id')->first()?->load('children');
        $copyLastFile = (bool) request('copyLastFile');

        return [
            Group::make()
                ->maxWidth('3xl')
                ->schema([
                    Grid::make()
                        ->schema([
                            DatePicker::make('admittance_date')
                                ->label(__('beneficiary.section.monitoring.labels.admittance_date'))
                                ->default(self::getDefaultValue($copyLastFile, $lastFile, 'admittance_date')),

                            TextInput::make('admittance_disposition')
                                ->label(__('beneficiary.section.monitoring.labels.admittance_disposition'))
                                ->placeholder(__('beneficiary.section.monitoring.placeholders.admittance_disposition'))
                                ->default(self::getDefaultValue($copyLastFile, $lastFile, 'admittance_disposition'))
                                ->maxLength(100),
                        ]),

                    Textarea::make('services_in_center')
                        ->label(__('beneficiary.section.monitoring.labels.services_in_center'))
                        ->placeholder(__('beneficiary.section.monitoring.placeholders.services_in_center'))
                        ->default(self::getDefaultValue($copyLastFile, $lastFile, 'services_in_center'))
                        ->maxLength(2500),

                    ...self::getGeneralMonitoringDataFields($lastFile, $copyLastFile),

                    Placeholder::make('progress_placeholder')
                        ->label(__('beneficiary.section.monitoring.headings.progress')),

                    Textarea::make('progress')
                        ->label(__('beneficiary.section.monitoring.labels.progress'))
                        ->placeholder(__('beneficiary.section.monitoring.placeholders.progress'))
                        ->default(self::getDefaultValue($copyLastFile, $lastFile, 'progress'))
                        ->maxLength(2500),

                    Placeholder::make('observation_placeholder')
                        ->label(__('beneficiary.section.monitoring.headings.observation')),

                    Textarea::make('observation')
                        ->label(__('beneficiary.section.monitoring.labels.observation'))
                        ->placeholder(__('beneficiary.section.monitoring.placeholders.observation'))
                        ->default(self::getDefaultValue($copyLastFile, $lastFile, 'observation'))
                        ->maxLength(2500),

                ]),
        ];
    }

    private static function getGeneralMonitoringDataFields(?Monitoring $lastFile, bool $copyLastFile = false): array
    {
        $formFields = [];
        $fields = [
            'protection_measures',
            'health_measures',
            'legal_measures',
            'psychological_measures',
            'aggressor_relationship',
            'others',
        ];

        foreach ($fields as $field) {
            $lastFieldData = null;
            if ($copyLastFile) {
                $lastFieldData = $lastFile?->$field;
            }
            $formFields[] = Placeholder::make($field)
                ->label(__(sprintf('beneficiary.section.monitoring.headings.%s', $field)));

            $formFields[] = Textarea::make($field . '.objection')
                ->label(__('beneficiary.section.monitoring.labels.objection'))
                ->placeholder(__('beneficiary.section.monitoring.placeholders.add_details'))
                ->default($lastFieldData['objection'] ?? null)
                ->maxLength(1500);

            $formFields[] = Textarea::make($field . '.activity')
                ->label(__('beneficiary.section.monitoring.labels.activity'))
                ->placeholder(__('beneficiary.section.monitoring.placeholders.add_details'))
                ->default($lastFieldData['activity'] ?? null)
                ->maxLength(1500);

            $formFields[] = Textarea::make($field . '.conclusion')
                ->label(__('beneficiary.section.monitoring.labels.conclusion'))
                ->placeholder(__('beneficiary.section.monitoring.placeholders.add_details'))
                ->default($lastFieldData['conclusion'] ?? null)
                ->maxLength(1500);
        }

        return $formFields;
    }

    private static function getDefaultValue(bool $copyLastFile, ?Monitoring $lastFile, string $field): string | int | null
    {
        if (! $copyLastFile) {
            return null;
        }

        return $lastFile?->$field ?? null;
    }
}
