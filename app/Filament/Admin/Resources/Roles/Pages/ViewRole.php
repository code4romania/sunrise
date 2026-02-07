<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles\Pages;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\Roles\RoleResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list').' - '.__('nomenclature.headings.roles').': '.$this->getRecord()->name;
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        $actions = [];

        if ($record->status === GeneralStatus::ACTIVE) {
            $actions[] = Action::make('inactivate')
                ->label(__('nomenclature.actions.change_status.inactivate'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('nomenclature.headings.inactivate_role_modal'))
                ->modalDescription(__('nomenclature.helper_texts.inactivate_role_modal'))
                ->modalSubmitActionLabel(__('nomenclature.actions.change_status.inactivate_role_modal'))
                ->action(fn () => $record->update(['status' => GeneralStatus::INACTIVE]))
                ->successNotificationTitle(__('nomenclature.actions.change_status.inactivate'))
                ->after(fn () => $this->redirect(RoleResource::getUrl('view', ['record' => $record])));
        } else {
            $actions[] = Action::make('activate')
                ->label(__('nomenclature.actions.change_status.activate'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(fn () => $record->update(['status' => GeneralStatus::ACTIVE]))
                ->successNotificationTitle(__('nomenclature.actions.change_status.activate'))
                ->after(fn () => $this->redirect(RoleResource::getUrl('view', ['record' => $record])));
        }

        return $actions;
    }
}
