<?php

declare(strict_types=1);

namespace App\Filament\Admin\Schemas;

use App\Enums\CounselingSheet;
use App\Enums\GeneralStatus;
use App\Forms\Components\Select;
use App\Models\Service;
use App\Models\ServiceIntervention;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components(self::getFormComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['organizationServices']))
            ->columns(self::getTableColumns())
            ->recordActions(self::getTableActions())
            ->headerActions(self::getTableHeaderActions())
            ->filters(self::getTableFilters())
            ->heading(__('nomenclature.headings.service_table'))
            ->emptyStateHeading(__('nomenclature.labels.empty_state_service_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getFormComponents(): array
    {
        return [
            Section::make()
                ->maxWidth('3xl')
                ->schema([
                    TextInput::make('name')
                        ->label(__('service.field.name'))
                        ->columnSpanFull()
                        ->maxLength(200)
                        ->required(),

                    Select::make('counseling_sheet')
                        ->label(__('nomenclature.labels.counseling_sheet'))
                        ->options(CounselingSheet::options()),
                ]),

            Section::make()
                ->schema([
                    Repeater::make('serviceInterventions')
                        ->relationship('serviceInterventions')
                        ->label(__('nomenclature.headings.service_intervention'))
                        ->helperText(__('nomenclature.helper_texts.service_interventions'))
                        ->reorderable()
                        ->orderColumn('sort')
                        ->columnSpanFull()
                        ->addActionLabel(__('nomenclature.actions.add_intervention'))
                        ->minItems(1)
                        ->schema([
                            TextEntry::make('index')
                                ->label(__('nomenclature.labels.nr')),

                            TextInput::make('name')
                                ->label(__('nomenclature.labels.intervention_name'))
                                ->maxLength(200)
                                ->required()
                                ->columnSpan(2),

                            Toggle::make('status')
                                ->label(__('nomenclature.labels.status'))
                                ->live()
                                ->default(true)
                                ->afterStateUpdated(function (bool $state) {
                                    if (! $state) {
                                        // TODO: fix this
                                        dd('Modal cu inactivare de hard confirmation');
                                    }
                                })
                                ->formatStateUsing(fn ($state) => $state ?? true)
                                ->dehydrated(),
                        ])
                        ->columns(4)
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                        ->deleteAction(
                            fn (\Filament\Actions\Action $action) => $action
                                ->disabled(function (array $arguments, Repeater $component): bool {
                                    $items = $component->getState();
                                    $currentItem = $items[$arguments['item']] ?? null;

                                    if (! isset($currentItem['id'])) {
                                        return false;
                                    }

                                    $serviceIntervention = ServiceIntervention::where('id', $currentItem['id'])
                                        ->withCount('organizationIntervention')
                                        ->first();

                                    if (! $serviceIntervention) {
                                        return false;
                                    }

                                    return $serviceIntervention->organization_intervention_count > 0;
                                })
                        ),
                ]),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('nomenclature.labels.name'))
                ->searchable(),

            TextColumn::make('institutions_count')
                ->label(__('nomenclature.labels.institutions')),

            TextColumn::make('organization_services_count')
                ->label(__('nomenclature.labels.centers')),

            TextColumn::make('status')
                ->label(__('nomenclature.labels.status'))
                ->badge(),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->label(__('general.action.view_details'))
                ->url(fn (Service $record) => \App\Filament\Admin\Resources\ServiceResource::getUrl('view', ['record' => $record])),
        ];
    }

    public static function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('nomenclature.actions.add_service'))
                ->url(\App\Filament\Admin\Resources\ServiceResource::getUrl('create')),
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->options(GeneralStatus::options()),
        ];
    }
}
