<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToMonitoring;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Forms\Components\DatePicker;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
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
    use PreventSubmitFormOnEnter;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForMonitoringFileEdit($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.edit_general');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('monitoring.headings.general'));
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
        return [
            Group::make()
                ->maxWidth('3xl')
                ->schema([
                    Grid::make()
                        ->schema([
                            DatePicker::make('admittance_date')
                                ->label(__('monitoring.labels.admittance_date'))
                                ->default(self::getParent()?->created_at),

                            TextInput::make('admittance_disposition')
                                ->label(__('monitoring.labels.admittance_disposition'))
                                ->placeholder(__('monitoring.placeholders.admittance_disposition'))
                                ->maxLength(100),
                        ]),

                    Textarea::make('services_in_center')
                        ->label(__('monitoring.labels.services_in_center'))
                        ->placeholder(__('monitoring.placeholders.services_in_center'))
                        ->maxLength(2500),

                    ...self::getGeneralMonitoringDataFields(),

                    Placeholder::make('progress_placeholder')
                        ->label(__('monitoring.headings.progress')),

                    Textarea::make('progress')
                        ->label(__('monitoring.labels.progress'))
                        ->placeholder(__('monitoring.placeholders.progress'))
                        ->maxLength(2500),

                    Placeholder::make('observation_placeholder')
                        ->label(__('monitoring.headings.observation')),

                    Textarea::make('observation')
                        ->label(__('monitoring.labels.observation'))
                        ->placeholder(__('monitoring.placeholders.observation'))
                        ->maxLength(2500),

                ]),
        ];
    }

    private static function getGeneralMonitoringDataFields(): array
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
            $formFields[] = Placeholder::make($field)
                ->label(__(\sprintf('monitoring.headings.%s', $field)));

            $formFields[] = Textarea::make($field . '.objection')
                ->label(__('monitoring.labels.objection'))
                ->placeholder(__('monitoring.placeholders.add_details'))
                ->maxLength(1500);

            $formFields[] = Textarea::make($field . '.activity')
                ->label(__('monitoring.labels.activity'))
                ->placeholder(__('monitoring.placeholders.add_details'))
                ->maxLength(1500);

            $formFields[] = Textarea::make($field . '.conclusion')
                ->label(__('monitoring.labels.conclusion'))
                ->placeholder(__('monitoring.placeholders.add_details'))
                ->maxLength(1500);
        }

        return $formFields;
    }
}
