<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\Monitoring\Pages;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToMonitoring;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\MonitoringResource;
use App\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditGeneral extends EditRecord
{
    use PreventSubmitFormOnEnter;
    use RedirectToMonitoring;

    protected static string $resource = MonitoringResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.edit_general');
    }

    public function getBreadcrumbs(): array
    {
        $parent = $this->getParentRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $parent]) => $parent?->getBreadcrumb() ?? '',
            CaseResource::getUrl('edit_case_monitoring', ['record' => $parent]) => __('monitoring.titles.list'),
            MonitoringResource::getUrl('view', ['beneficiary' => $parent, 'record' => $this->getRecord()]) => __('monitoring.titles.view', ['file_number' => $this->getRecord()->number ?? (string) $this->getRecord()->id]),
            '' => __('monitoring.titles.edit_general'),
        ];
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('monitoring.headings.general'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->maxWidth('3xl')
                ->schema([
                    Group::make()
                        ->maxWidth('3xl')
                        ->schema([
                            Grid::make()->schema([
                                DatePicker::make('admittance_date')
                                    ->label(__('monitoring.labels.admittance_date'))
                                    ->default($this->getParentRecord()?->created_at),
                                TextInput::make('admittance_disposition')
                                    ->label(__('monitoring.labels.admittance_disposition'))
                                    ->maxLength(100),
                            ]),
                            Textarea::make('services_in_center')
                                ->label(__('monitoring.labels.services_in_center'))
                                ->maxLength(2500),
                            ...$this->generalSectionFields(),
                            Placeholder::make('progress_placeholder')->label(__('monitoring.headings.progress')),
                            Textarea::make('progress')
                                ->label(__('monitoring.labels.progress'))
                                ->maxLength(2500),
                            Placeholder::make('observation_placeholder')->label(__('monitoring.headings.observation')),
                            Textarea::make('observation')
                                ->label(__('monitoring.labels.observation'))
                                ->maxLength(2500),
                        ]),
                ]),
        ]);
    }

    /**
     * @return array<int, Placeholder|Textarea>
     */
    private function generalSectionFields(): array
    {
        $fields = [
            'protection_measures',
            'health_measures',
            'legal_measures',
            'psychological_measures',
            'aggressor_relationship',
            'others',
        ];
        $schema = [];

        foreach ($fields as $field) {
            $schema[] = Placeholder::make($field)
                ->label(__('monitoring.headings.'.$field));
            $schema[] = Textarea::make($field.'.objection')
                ->label(__('monitoring.labels.objection'))
                ->maxLength(1500);
            $schema[] = Textarea::make($field.'.activity')
                ->label(__('monitoring.labels.activity'))
                ->maxLength(1500);
            $schema[] = Textarea::make($field.'.conclusion')
                ->label(__('monitoring.labels.conclusion'))
                ->maxLength(1500);
        }

        return $schema;
    }
}
