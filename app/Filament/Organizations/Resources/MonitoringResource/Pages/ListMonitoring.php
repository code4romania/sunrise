<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Actions\BackAction;
use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Models\Monitoring;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use App\Tables\Columns\DateColumn;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListMonitoring extends ListRecords
{
    use HasParentResource;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)
            ->getBreadcrumbs('monitorings.index');
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.list');
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view', ['record' => $this->parent])),

            Actions\Action::make('open_modal')
                ->label(__('monitoring.actions.create'))
                ->visible(fn () => $this->parent->monitoring->count())
                ->modalHeading(__('monitoring.headings.modal_create'))
                ->modalDescription(__('monitoring.labels.modal_create_description'))
                ->modalSubmitAction(
                    Actions\Action::make('crete_from_last')
                        ->label(__('monitoring.actions.create_from_last'))
                        ->url(
                            fn () => self::getParentResource()::getUrl('monitorings.create', [
                                'parent' => $this->parent,
                                'copyLastFile' => 'copyLastFile',
                            ])
                        )
                )
                ->modalCancelAction(
                    Actions\Action::make('create_simple')
                        ->label(__('monitoring.actions.create_simple'))
                        ->url(
                            fn () => self::getParentResource()::getUrl('monitorings.create', [
                                'parent' => $this->parent,
                            ])
                        )
                ),

            Actions\CreateAction::make()
                ->label(__('monitoring.actions.create'))
                ->hidden(fn () => $this->parent->monitoring->count())
                ->url(
                    fn () => self::getParentResource()::getUrl('monitorings.create', [
                        'parent' => $this->parent,
                    ])
                ),
        ];
    }

    public function table(Table $table): Table
    {
        return $table->modifyQueryUsing(fn (Builder $query) => $query->with('beneficiary'))
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
            ->actions([
                ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->color('primary')
                    ->url(fn (Monitoring $record) => (static::getParentResource()::getUrl('monitorings.view', [
                        'parent' => $this->parent,
                        'record' => $record,
                    ]))),
            ])
            ->actionsColumnLabel(__('monitoring.headings.actions'))
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
                CreateAction::make()
                    ->label(__('monitoring.actions.create'))
                    ->url(
                        fn () => self::getParentResource()::getUrl('monitorings.create', [
                            'parent' => $this->parent,
                        ])
                    ),
            ]);
    }
}
