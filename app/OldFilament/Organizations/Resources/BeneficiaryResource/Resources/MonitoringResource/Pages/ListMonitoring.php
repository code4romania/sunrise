<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Resources\MonitoringResource\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\BeneficiaryResource\Resources\MonitoringResource;
use App\Models\Monitoring;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use App\Tables\Columns\DateColumn;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListMonitoring extends ListRecords
{
    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        $parentRecord = $this->getParentRecord();

        return BeneficiaryBreadcrumb::make($parentRecord)
            ->getBreadcrumbs('monitorings.index');
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.list');
    }

    protected function getHeaderActions(): array
    {
        $parentRecord = $this->getParentRecord();

        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view', ['record' => $parentRecord])),

            Action::make('open_modal')
                ->label(__('monitoring.actions.create'))
                ->visible(fn () => $parentRecord->monitoring->count())
                ->modalHeading(__('monitoring.headings.modal_create'))
                ->modalDescription(__('monitoring.labels.modal_create_description'))
                ->modalSubmitAction(
                    Action::make('crete_from_last')
                        ->label(__('monitoring.actions.create_from_last'))
                        ->url(
                            fn () => static::getResource()::getUrl('create', [
                                'beneficiary' => $parentRecord,
                                'copyLastFile' => 'copyLastFile',
                            ])
                        )
                )
                ->modalCancelAction(
                    Action::make('create_simple')
                        ->label(__('monitoring.actions.create_simple'))
                        ->url(
                            fn () => static::getResource()::getUrl('create', [
                                'beneficiary' => $parentRecord,
                            ])
                        )
                ),

            Actions\CreateAction::make()
                ->label(__('monitoring.actions.create'))
                ->hidden(fn () => $parentRecord->monitoring->count())
                ->url(
                    fn () => static::getResource()::getUrl('create', [
                        'beneficiary' => $parentRecord,
                    ])
                ),
        ];
    }

    public function table(Table $table): Table
    {
        $parentRecord = $this->getParentRecord();

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('beneficiary_id', $parentRecord->id)->with('beneficiary'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('monitoring.headings.id')),

                TextColumn::make('number')
                    ->label(__('monitoring.headings.file_number'))
                    ->sortable(),

                DateColumn::make('date')
                    ->label(__('monitoring.headings.date'))
                    ->sortable(),

                TextColumn::make('interval')
                    ->label(__('monitoring.headings.interval'))
                    ->sortable(),

                TextColumn::make('specialistsTeam.name_role')
                    ->label(__('monitoring.headings.team'))
                    ->sortable()
                    ->listWithLineBreaks(),
            ])
            ->recordActions([
                Actions\ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->color('primary')
                    ->url(fn (Monitoring $record) => static::getResource()::getUrl('view', [
                        'beneficiary' => $this->getParentRecord(),
                        'record' => $record,
                    ])),
            ])
            ->recordActionsColumnLabel(__('monitoring.headings.actions'))
            ->modifyQueryUsing(
                fn (Builder $query) => $query->with([
                    'specialistsTeam.user',
                    'specialistsTeam.role',
                ])
                    ->orderByDesc('id')
            )
            ->emptyStateHeading(__('monitoring.headings.empty_state_table'))
            ->emptyStateDescription(__('monitoring.labels.empty_state_table'))
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateActions([
                Actions\CreateAction::make()
                    ->label(__('monitoring.actions.create'))
                    ->url(
                        fn () => static::getResource()::getUrl('create', [
                            'beneficiary' => $this->getParentRecord(),
                        ])
                    ),
            ]);
    }
}
