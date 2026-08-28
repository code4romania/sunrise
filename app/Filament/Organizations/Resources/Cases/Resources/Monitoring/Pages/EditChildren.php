<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\Monitoring\Pages;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToMonitoring;
use App\Enums\ChildAggressorRelationship;
use App\Enums\MaintenanceSources;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\MonitoringResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use Carbon\Carbon;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditChildren extends EditRecord
{
    use PreventSubmitFormOnEnter;
    use RedirectToMonitoring;

    protected static string $resource = MonitoringResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.edit_children');
    }

    public function getBreadcrumbs(): array
    {
        $parent = $this->getParentRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $parent]) => $parent?->getBreadcrumb() ?? '',
            CaseResource::getUrl('edit_case_monitoring', ['record' => $parent]) => __('monitoring.titles.list'),
            MonitoringResource::getUrl('view', ['beneficiary' => $parent, 'record' => $this->getRecord()]) => __('monitoring.titles.view', ['file_number' => $this->getRecord()->number ?? (string) $this->getRecord()->id]),
            '' => __('monitoring.titles.edit_children'),
        ];
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('monitoring.headings.child_info'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->maxWidth('3xl')
                ->schema([
                    Placeholder::make('empty_state_children')
                        ->label(__('monitoring.headings.empty_state_children'))
                        ->visible(fn (Get $get): bool => ! $get('children')),
                    Repeater::make('children')
                        ->relationship('children')
                        ->hiddenLabel()
                        ->maxWidth('3xl')
                        ->deletable(false)
                        ->addable(false)
                        ->schema([
                            TextInput::make('name')
                                ->label(__('monitoring.labels.child_name'))
                                ->columnSpanFull(),
                            Grid::make()
                                ->schema([
                                    TextInput::make('status')
                                        ->label(__('monitoring.labels.status'))
                                        ->maxLength(70),
                                    TextInput::make('age')
                                        ->label(__('monitoring.labels.age'))
                                        ->readOnly()
                                        ->maxLength(2)
                                        ->mask('99'),
                                    DatePicker::make('birthdate')
                                        ->label(__('monitoring.labels.birthdate'))
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, callable $set): void {
                                            if (! is_string($state) || blank($state)) {
                                                $set('age', '');

                                                return;
                                            }

                                            try {
                                                $set('age', (string) Carbon::parse($state)->age);
                                            } catch (\Throwable) {
                                                $set('age', '');
                                            }
                                        }),
                                    Select::make('aggressor_relationship')
                                        ->label(__('monitoring.labels.aggressor_relationship'))
                                        ->placeholder(__('monitoring.placeholders.select_an_answer'))
                                        ->options(ChildAggressorRelationship::options()),
                                    Select::make('maintenance_sources')
                                        ->label(__('monitoring.labels.maintenance_sources'))
                                        ->placeholder(__('monitoring.placeholders.select_an_answer'))
                                        ->options(MaintenanceSources::options()),
                                    TextInput::make('location')
                                        ->label(__('monitoring.labels.location'))
                                        ->maxLength(100),
                                    Textarea::make('observations')
                                        ->label(__('monitoring.labels.observations'))
                                        ->maxLength(500)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ]),
        ]);
    }
}
