<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets;

use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\InterventionPlanResult;
use App\Models\Result;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class InterventionPlanResultsWidget extends TableWidget
{
    public ?Beneficiary $record = null;

    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $plan = $this->record?->interventionPlan;

        return $table
            ->query(
                $plan
                    ? $plan->results()->with(['result', 'user'])->getQuery()
                    : InterventionPlanResult::query()->whereRaw('1 = 0')
            )
            ->heading(__('intervention_plan.headings.results_table'))
            ->columns([
                TextColumn::make('result.name')
                    ->label(__('intervention_plan.headings.result')),
                TextColumn::make('user.full_name')
                    ->label(__('intervention_plan.headings.specialist')),
                TextColumn::make('started_at')
                    ->label(__('intervention_plan.headings.started_at'))
                    ->date('d.m.Y'),
                TextColumn::make('ended_at')
                    ->label(__('intervention_plan.headings.ended_at'))
                    ->formatStateUsing(fn (InterventionPlanResult $record) => $record->ended_at?->format('d.m.Y') ?? '—'),
                TextColumn::make('observations')
                    ->label(__('intervention_plan.headings.observations'))
                    ->html()
                    ->limit(50),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->label(__('intervention_plan.actions.add_result'))
                    ->modalHeading(__('intervention_plan.actions.add_result'))
                    ->model(InterventionPlanResult::class)
                    ->mutateDataUsing(function (array $data): array {
                        $data['intervention_plan_id'] = $this->record?->interventionPlan?->id;

                        return $data;
                    })
                    ->schema([
                        Grid::make()
                            ->columns(3)
                            ->schema([
                                Select::make('result_id')
                                    ->label(__('intervention_plan.labels.result'))
                                    ->options(Result::query()->active()->pluck('name', 'id')->all())
                                    ->required(),
                                Select::make('user_id')
                                    ->label(__('intervention_plan.labels.specialist'))
                                    ->options(User::getTenantOrganizationUsers()->all()),
                                DatePicker::make('started_at')
                                    ->label(__('intervention_plan.labels.started_at'))
                                    ->required(),
                                DatePicker::make('ended_at')
                                    ->label(__('intervention_plan.labels.ended_at'))
                                    ->disabled(fn (Get $get): bool => (bool) $get('retried')),
                                Checkbox::make('retried')
                                    ->label(__('intervention_plan.labels.retried'))
                                    ->live(),
                                Checkbox::make('lost_from_monitoring')
                                    ->label(__('intervention_plan.labels.lost_from_monitoring'))
                                    ->disabled(fn (Get $get): bool => (bool) $get('retried')),
                                DatePicker::make('retried_at')
                                    ->label(__('intervention_plan.labels.retried_at'))
                                    ->visible(fn (Get $get): bool => (bool) $get('retried')),
                            ]),
                        RichEditor::make('observations')
                            ->label(__('intervention_plan.labels.result_observations'))
                            ->placeholder(__('intervention_plan.placeholders.result_observations')),
                    ])
                    ->createAnother(false),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_result_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_result_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public static function canView(): bool
    {
        return true;
    }
}
