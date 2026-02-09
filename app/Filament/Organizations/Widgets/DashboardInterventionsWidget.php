<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Widgets;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\BeneficiaryIntervention;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class DashboardInterventionsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        $tenant = Filament::getTenant();

        return $table
            ->query(
                BeneficiaryIntervention::query()
                    ->whereHas(
                        'organizationServiceIntervention.organizationService',
                        fn (Builder $q) => $q->where('organization_id', $tenant?->id)
                    )
                    ->when(
                        ! auth()->user()?->isNgoAdmin(),
                        fn (Builder $query): Builder => $query->where(
                            fn (Builder $q): Builder => $q
                                ->whereHas(
                                    'specialist',
                                    fn (Builder $sq) => $sq->where('user_id', auth()->id())
                                )
                                ->orWhereHas(
                                    'beneficiary.specialistsMembers',
                                    fn (Builder $sq) => $sq->where('users.id', auth()->id())
                                )
                        )
                    )
                    ->with([
                        'organizationServiceIntervention.serviceInterventionWithoutStatusCondition.service',
                        'interventionService',
                        'interventionService.interventionPlan',
                        'beneficiary',
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
                        fn (BeneficiaryIntervention $record): string => CaseResource::getUrl('view', [
                            'record' => $record->beneficiary,
                        ])
                    )
                    ->color('primary')
                    ->searchable(true, fn (Builder $query, string $search): Builder => $query->where(
                        fn (Builder $q) => $q->whereHas(
                            'beneficiary',
                            fn (Builder $b) => $b->where('full_name', 'like', '%'.$search.'%')
                        )->orWhereHas(
                            'specialist.user',
                            fn (Builder $b) => $b->where('first_name', 'like', '%'.$search.'%')
                                ->orWhere('last_name', 'like', '%'.$search.'%')
                        )
                    )),

                TextColumn::make('specialist.user.full_name')
                    ->label(__('intervention_plan.labels.specialist')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('intervention_plan.actions.view_intervention'))
                    ->url(
                        fn (BeneficiaryIntervention $record): string => CaseResource::getUrl('view_intervention_plan', [
                            'record' => $record->beneficiary,
                        ])
                    ),
            ])
            ->recordUrl(
                fn (BeneficiaryIntervention $record): string => CaseResource::getUrl('view_intervention_plan', [
                    'record' => $record->beneficiary,
                ])
            )
            ->recordActionsColumnLabel(__('intervention_plan.labels.actions'))
            ->paginationPageOptions([5, 10, 15])
            ->defaultPaginationPageOption(15)
            ->emptyStateHeading(__('intervention_plan.headings.dashboard_intervention_table_empty_state'))
            ->emptyStateDescription('')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
