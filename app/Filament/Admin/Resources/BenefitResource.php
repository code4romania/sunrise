<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\BenefitResource\Pages;
use App\Forms\Components\TableRepeater;
use App\Models\Benefit;
use App\Models\BenefitService;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BenefitResource extends Resource
{
    protected static ?string $model = Benefit::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('nomenclature.labels.benefit_name'))
                            ->maxWidth('3xl'),
                        TableRepeater::make('benefitTypes')
                            ->relationship('benefitTypes')
                            ->label(__('nomenclature.headings.benefit_types'))
                            ->columnSpanFull()
                            ->hideLabels()
                            ->addActionLabel(__('nomenclature.actions.add_benefit_type'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('nomenclature.labels.benefit_type_name')),
                                Toggle::make('status')
                                    ->default(true)
                                    ->hiddenLabel(),
                            ])
                            ->deleteAction(
                                fn (Action $action) => $action
                                    ->disabled(function (array $arguments, TableRepeater $component): bool {
                                        $items = $component->getState();
                                        $currentItem = $items[$arguments['item']];

                                        if (! $currentItem['id']) {
                                            return false;
                                        }

                                        return (bool) BenefitService::query()
                                            ->whereJsonContains('benefit_types', \sprintf('%s', $currentItem['id']))
                                            ->count();
                                    })
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_benefit'))
                    ->url(self::getUrl('create')),
            ])
            ->heading(__('nomenclature.headings.benefit_table'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.benefit')),

                TextColumn::make('institutions_count')
                    ->label(__('nomenclature.labels.institutions')),

                TextColumn::make('organizations_count')
                    ->label(__('nomenclature.labels.centers')),

                TextColumn::make('status')
                    ->label(__('nomenclature.labels.status')),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->url(fn (Benefit $record) => BenefitResource::getUrl('view', ['record' => $record])),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GeneralStatus::options()),
            ])
            ->emptyStateHeading(__('nomenclature.headings.empty_state_benefit_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBenefits::route('/'),
            'create' => Pages\CreateBenefit::route('/create'),
            'view' => Pages\ViewBenefit::route('/{record}'),
            'edit' => Pages\EditBenefit::route('/{record}/edit'),
        ];
    }
}
