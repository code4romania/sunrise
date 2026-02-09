<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Services\Tables;

use App\Enums\GeneralStatus;
use App\Models\OrganizationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->with(['serviceWithoutStatusCondition'])
                    ->withCount(['interventions'])
            )
            ->columns(self::getColumns())
            ->headerActions([
                CreateAction::make()
                    ->label(__('service.actions.create')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('general.action.view_details')),
                Action::make('deactivate')
                    ->label(__('service.actions.change_status.inactivate'))
                    ->color('danger')
                    ->visible(fn (OrganizationService $record): bool => $record->status === GeneralStatus::ACTIVE)
                    ->requiresConfirmation()
                    ->modalHeading(__('service.headings.inactivate_modal'))
                    ->modalDescription(__('service.helper_texts.inactivate_modal'))
                    ->modalSubmitActionLabel(__('service.actions.change_status.inactivate_modal'))
                    ->action(function (OrganizationService $record): void {
                        $record->update(['status' => GeneralStatus::INACTIVE]);
                    }),
                Action::make('activate')
                    ->label(__('service.actions.change_status.activate'))
                    ->color('success')
                    ->visible(fn (OrganizationService $record): bool => $record->status === GeneralStatus::INACTIVE)
                    ->action(function (OrganizationService $record): void {
                        $record->update(['status' => GeneralStatus::ACTIVE]);
                    }),
            ])
            ->emptyStateHeading(__('service.headings.empty_state_table'))
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    /**
     * @return array<int, \Filament\Tables\Columns\TextColumn>
     */
    public static function getColumns(): array
    {
        return [
            TextColumn::make('serviceWithoutStatusCondition.name')
                ->label(__('service.labels.name')),

            TextColumn::make('interventions_count')
                ->label(__('service.labels.interventions')),

            TextColumn::make('status')
                ->label(__('service.labels.status'))
                ->badge(),
        ];
    }
}
