<?php

declare(strict_types=1);

namespace App\Filament\Admin\Schemas;

use App\Enums\GeneralStatus;
use App\Models\Benefit;
use App\Models\BenefitService;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BenefitResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components(self::getFormComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions(self::getTableHeaderActions())
            ->heading(__('nomenclature.headings.benefit_table'))
            ->columns(self::getTableColumns())
            ->recordActions(self::getTableActions())
            ->filters(self::getTableFilters())
            ->emptyStateHeading(__('nomenclature.headings.empty_state_benefit_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getFormComponents(): array
    {
        return [
            Section::make()
                ->schema([
                    TextInput::make('name')
                        ->label(__('nomenclature.labels.benefit_name'))
                        ->maxWidth('3xl')
                        ->maxLength(200)
                        ->required(),

                    Repeater::make('benefitTypes')
                        ->relationship('benefitTypes')
                        ->label(__('nomenclature.headings.benefit_types'))
                        ->helperText(__('nomenclature.helper_texts.benefit_types'))
                        ->reorderable()
                        ->orderColumn('sort')
                        ->columnSpanFull()
                        ->minItems(1)
                        ->addActionLabel(__('nomenclature.actions.add_benefit_type'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('nomenclature.labels.benefit_type_name'))
                                ->maxLength(200)
                                ->required()
                                ->columnSpan(2),

                            Toggle::make('status')
                                ->label(__('nomenclature.labels.status'))
                                ->live()
                                ->default(true)
                                ->formatStateUsing(fn ($state) => $state ?? true)
                                ->dehydrated(),
                        ])
                        ->columns(3)
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                        ->collapsible()
                        ->deleteAction(
                            fn (\Filament\Actions\Action $action) => $action
                                ->disabled(function (array $arguments, Repeater $component): bool {
                                    $items = $component->getState();
                                    $currentItem = $items[$arguments['item']] ?? null;

                                    if (! isset($currentItem['id'])) {
                                        return false;
                                    }

                                    return (bool) BenefitService::query()
                                        ->whereJsonContains('benefit_types', \sprintf('%s', $currentItem['id']))
                                        ->count();
                                })
                        ),
                ]),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('nomenclature.labels.benefit')),

            TextColumn::make('institutions_count')
                ->label(__('nomenclature.labels.institutions')),

            TextColumn::make('organizations_count')
                ->label(__('nomenclature.labels.centers')),

            TextColumn::make('status')
                ->label(__('nomenclature.labels.status')),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->label(__('general.action.view_details'))
                ->url(fn (Benefit $record) => \App\Filament\Admin\Resources\BenefitResource::getUrl('view', ['record' => $record])),
        ];
    }

    public static function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('nomenclature.actions.add_benefit'))
                ->url(\App\Filament\Admin\Resources\BenefitResource::getUrl('create')),
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
