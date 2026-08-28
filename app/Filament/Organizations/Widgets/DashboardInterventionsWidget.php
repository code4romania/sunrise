<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Widgets;

use App\Enums\DashboardIntervalFilter;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\BeneficiaryIntervention;
use App\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class DashboardInterventionsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => BeneficiaryIntervention::query()
                    ->whereHas(
                        'beneficiary',
                    )
                    ->when(
                        ! auth()->user()->isNgoAdmin(),
                        fn (Builder $query) => $query->where(
                            fn (Builder $query) => $query
                                ->whereHas(
                                    'specialist',
                                    fn (Builder $query) => $query->where('user_id', auth()->id())
                                )
                                ->orWhereHas(
                                    'beneficiary.managerTeam',
                                    fn (Builder $query) => $query->where('user_id', auth()->id())
                                )
                        )
                    )
                    ->with([
                        'organizationServiceIntervention.serviceInterventionWithoutStatusCondition.service',
                        'interventionService',
                        'interventionService.interventionPlan',
                        'beneficiary',
                        'interventionPlan',
                        'specialist.user',
                    ])
            )
            ->heading(__('intervention_plan.headings.dashboard_intervention_table'))
            ->searchPlaceholder(__('intervention_plan.placeholders.search_by_beneficiary_or_specialist'))
            ->columns([
                TextColumn::make('organizationServiceIntervention.serviceInterventionWithoutStatusCondition.name')
                    ->label(__('intervention_plan.labels.intervention')),

                TextColumn::make('organizationServiceIntervention.serviceInterventionWithoutStatusCondition.service.name')
                    ->label(__('intervention_plan.labels.service')),

                TextColumn::make('interval')
                    ->label(__('intervention_plan.labels.interval')),

                TextColumn::make('beneficiary.full_name')
                    ->label(__('intervention_plan.labels.beneficiary'))
                    ->url(
                        fn (BeneficiaryIntervention $record) => CaseResource::getUrl('view', [
                            'record' => $record->beneficiary,
                        ])
                    )
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('specialist.user.full_name')
                    ->label(__('intervention_plan.labels.specialist'))
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('intervention_plan.actions.view_intervention'))
                    ->url(
                        fn (BeneficiaryIntervention $record) => CaseResource::getUrl('view_intervention_plan', [
                            'record' => $record->beneficiary,
                        ])
                    ),
            ])
            ->recordUrl(
                fn (BeneficiaryIntervention $record) => CaseResource::getUrl('view_intervention_plan', [
                    'record' => $record->beneficiary,
                ])
            )
            ->recordActionsColumnLabel(__('intervention_plan.labels.actions'))
            ->filters([
                SelectFilter::make('selected_interval')
                    ->label(__('intervention_plan.labels.selected_interval'))
                    ->options(DashboardIntervalFilter::options())
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;
                        if (DashboardIntervalFilter::isValue($value, DashboardIntervalFilter::ONE_WEEK)) {
                            return $query->where('start_date_interval', '<=', date('Y-m-d', strtotime('+1 week')))
                                ->where('end_date_interval', '>=', date('Y-m-d'));
                        }

                        if (DashboardIntervalFilter::isValue($value, DashboardIntervalFilter::TOMORROW)) {
                            return $query->where('start_date_interval', '<=', date('Y-m-d', strtotime('+1 day')))
                                ->where('end_date_interval', '>=', date('Y-m-d'));
                        }

                        return $query->where('start_date_interval', '<=', date('Y-m-d'))
                            ->where('end_date_interval', '>=', date('Y-m-d'));
                    }),
            ])
            ->paginationPageOptions([5, 10, 15])
            ->emptyStateHeading(__('intervention_plan.headings.dashboard_intervention_table_empty_state'))
            ->emptyStateDescription('')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
