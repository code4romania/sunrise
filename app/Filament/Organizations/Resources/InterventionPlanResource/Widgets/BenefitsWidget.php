<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Widgets;

use App\Forms\Components\Select;
use App\Models\Benefit;
use App\Models\BenefitType;
use App\Models\InterventionPlan;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Get;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BenefitsWidget extends BaseWidget
{
    public ?InterventionPlan $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record->benefits()
                    ->with('benefit.benefitTypes')
            )
            ->heading(__('intervention_plan.headings.benefit_services'))
            ->headerActions([
                CreateAction::make()
                    ->label(__('intervention_plan.actions.add_service'))
                    ->modalHeading(__('intervention_plan.headings.add_benefit_modal'))
                    ->form($this->getBenefitSchema())
                    ->createAnother(false),
            ])
            ->columns([
                TextColumn::make('benefit.name')
                    ->label(__('intervention_plan.headings.benefit_name')),

                TextColumn::make('benefit_types')
                    ->label(__('intervention_plan.headings.benefit_type'))
                    ->formatStateUsing(
                        fn ($state) => collect(explode(',', $state))
                            ->map(fn ($item) => BenefitType::find((int) trim($item))->name)
                            ->join(', ')
                    ),

                TextColumn::make('description')
                    ->label(__('intervention_plan.headings.benefit_description'))
                    ->html(),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('intervention_plan.actions.edit'))
                    ->form($this->getBenefitSchema())
                    ->extraModalFooterActions([
                        DeleteAction::make()
                            ->label(__('intervention_plan.actions.delete_benefit'))
                            ->outlined()
                            ->cancelParentActions()
                            ->modalHeading(__('intervention_plan.headings.delete_benefit_modal'))
                            ->modalDescription(fn ($record) => $record->benefit->name)
                            ->modalSubmitActionLabel(__('intervention_plan.actions.delete_benefit')),
                    ]),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_benefit_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_benefit_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public function getBenefitSchema(): array
    {
        return [
            Select::make('benefit_id')
                ->label(__('intervention_plan.labels.benefit_category'))
                ->relationship('benefit', 'name')
                ->searchable()
                ->preload()
                ->live(),

            CheckboxList::make('benefit_types')
                ->label(__('intervention_plan.labels.benefit_types'))
                ->options(function (Get $get) {
                    $benefitID = (int) $get('benefit_id');

                    return $benefitID ?
                        Benefit::find($benefitID)
                            ->benefitTypes
                            ->pluck('name', 'id') :
                        [];
                }),

            RichEditor::make('description')
                ->hiddenLabel()
                ->placeholder(__('intervention_plan.placeholders.benefit_description')),

            Hidden::make('intervention_plan_id')
                ->default(fn () => $this->record->id),
        ];
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.benefit_services');
    }
}
