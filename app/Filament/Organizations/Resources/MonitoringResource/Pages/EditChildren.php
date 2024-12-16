<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Concerns\RedirectToMonitoring;
use App\Enums\ChildAggressorRelationship;
use App\Enums\MaintenanceSources;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Forms\Components\DateInput;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditChildren extends EditRecord
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
        return __('monitoring.titles.edit_children');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('monitoring.headings.child_info'));
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
            Placeholder::make('empty_state_children')
                ->label(__('monitoring.headings.empty_state_children'))
                ->visible(fn (Get $get) => ! $get('children')),

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
                                ->maxLength(2)
                                ->mask('99'),

                            DateInput::make('birthdate')
                                ->label(__('monitoring.labels.birthdate')),

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
                                ->placeholder(__('monitoring.placeholders.location'))
                                ->maxLength(100),

                            Textarea::make('observations')
                                ->label(__('monitoring.labels.observations'))
                                ->placeholder(__('monitoring.placeholders.observations'))
                                ->maxLength(500)
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }
}
