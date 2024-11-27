<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Widgets;

use App\Forms\Components\DatePicker;
use App\Forms\Components\Select;
use App\Models\InterventionPlan;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Get;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ResultsWidget extends BaseWidget
{
    public ?InterventionPlan $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record
                    ->results()
                    ->with(['result', 'user'])
            )
            ->heading(__('intervention_plan.headings.results_table'))
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('intervention_plan.actions.add_result'))
                    ->createAnother(false)
                    ->form($this->getFormSchema())
                    ->modalHeading(__('intervention_plan.actions.add_result')),
            ])
            ->columns([
                TextColumn::make('result.name')
                    ->label(__('intervention_plan.headings.result')),

                TextColumn::make('user.full_name')
                    ->label(__('intervention_plan.headings.specialist')),

                TextColumn::make('started_at')
                    ->label(__('intervention_plan.headings.started_at')),

                TextColumn::make('ended_at')
                    ->label(__('intervention_plan.headings.ended_at'))
                    ->state(function ($record) {
                        if ($record->ended_at) {
                            return $record->ended_at;
                        }

                        if ($record->retried) {
                            return __('intervention_plan.labels.retried_date', ['date' => $record->retried_at]);
                        }

                        if ($record->lost_from_monitoring) {
                            return __('intervention_plan.labels.lost_from_monitoring_column');
                        }

                        return null;
                    }),

                TextColumn::make('observations')
                    ->label(__('intervention_plan.headings.observations'))
                    ->html(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('general.action.change'))
                    ->form($this->getFormSchema())
                    ->modalHeading(__('intervention_plan.headings.edit_result'))
                    ->extraModalFooterActions([
                        DeleteAction::make()
                            ->cancelParentActions()
                            ->label(__('intervention_plan.actions.delete_result'))
                            ->modalHeading(__('intervention_plan.actions.delete_result'))
                            ->modalSubmitActionLabel(__('intervention_plan.actions.delete_result'))
                            ->icon(null),
                    ]),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_result_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_result_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public function getFormSchema(): array
    {
        return [
            Grid::make()
                ->columns(3)
                ->schema([
                    Select::make('result_id')
                        ->label(__('intervention_plan.labels.result'))
                        ->relationship('activeResult', 'name')
                        ->required(),

                    Select::make('user_id')
                        ->label(__('intervention_plan.labels.specialist'))
                        ->options(User::getTenantOrganizationUsers()),

                    DatePicker::make('started_at')
                        ->label(__('intervention_plan.labels.started_at'))
                        ->required(),

                    DatePicker::make('ended_at')
                        ->label(__('intervention_plan.labels.ended_at'))
                        ->disabled(fn (Get $get) => $get('retried')),

                    Checkbox::make('retried')
                        ->label(__('intervention_plan.labels.retried'))
                        ->live(),

                    Checkbox::make('lost_from_monitoring')
                        ->label(__('intervention_plan.labels.lost_from_monitoring'))
                        ->disabled(fn (Get $get) => $get('retried')),

                    DatePicker::make('retried_at')
                        ->label(__('intervention_plan.labels.retried_at'))
                        ->visible(fn (Get $get) => $get('retried')),

                    Hidden::make('intervention_plan_id')
                        ->default($this->record->id),

                ]),

            RichEditor::make('observations')
                ->label(__('intervention_plan.labels.result_observations'))
                ->placeholder(__('intervention_plan.placeholders.observations')),
        ];
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.results_table');
    }
}
