<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Models\Monitoring;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
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
        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForMonitoring();
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.monitoring.titles.list');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('open_modal')
                ->label(__('beneficiary.section.monitoring.actions.create'))
                ->visible(fn () => $this->parent->monitoring->count())
                ->modalHeading(__('beneficiary.section.monitoring.headings.modal_create'))
                ->modalDescription(__('beneficiary.section.monitoring.labels.modal_create_description'))
                ->modalSubmitAction(
                    Actions\Action::make('crete_from_last')
                        ->label(__('beneficiary.section.monitoring.actions.create_from_last'))
                        ->url(
                            fn () => self::getParentResource()::getUrl('monitorings.create_from_last', [
                                'parent' => $this->parent,
                                'copyLastFile' => 1,
                            ])
                        )
                )
                ->modalCancelAction(
                    Actions\Action::make('create_simple')
                        ->label(__('beneficiary.section.monitoring.actions.create_simple'))
                        ->url(
                            fn () => self::getParentResource()::getUrl('monitorings.create', [
                                'parent' => $this->parent,
                            ])
                        )
                ),

            Actions\CreateAction::make()
                ->label(__('beneficiary.section.monitoring.actions.create'))
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
        return $table->columns([
            TextColumn::make('id')
                ->label(__('beneficiary.section.monitoring.headings.id')),
            TextColumn::make('number')
                ->label(__('beneficiary.section.monitoring.headings.file_number'))
                ->sortable(),
            TextColumn::make('date')
                ->label(__('beneficiary.section.monitoring.headings.date'))
                ->sortable(),
            TextColumn::make('start_date')
                ->label(__('beneficiary.section.monitoring.headings.interval'))
                ->sortable()
                ->formatStateUsing(fn ($record) => $record->start_date . ' - ' . $record->end_date),
            TextColumn::make('specialists')
                ->label(__('beneficiary.section.monitoring.headings.team'))
                ->sortable()
                ->limit(50)
                ->formatStateUsing(
                    fn ($record) => $record->specialists
                        ->map(fn ($specialist) => $specialist->user->getFilamentName() . ' (' .
                            $specialist->roles->map(fn ($role) => $role->label())->join(', ') . ')')
                        ->join('; ')
                ),
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
            ->actionsColumnLabel(__('beneficiary.section.monitoring.headings.actions'))
            ->modifyQueryUsing(fn (Builder $query) => $query->with('specialists')->orderByDesc('id'))
            ->emptyStateHeading(__('beneficiary.section.monitoring.headings.empty_state_table'))
            ->emptyStateDescription(__('beneficiary.section.monitoring.labels.empty_state_table'))
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateActions([
                CreateAction::make()
                    ->label(__('beneficiary.section.monitoring.actions.create'))
                    ->url(
                        fn () => self::getParentResource()::getUrl('monitorings.create', [
                            'parent' => $this->parent,
                        ])
                    ),
            ]);
    }
}
