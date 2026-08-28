<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Services\Pages;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\Services\ServiceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list').' - '.__('nomenclature.headings.service').': '.$this->getRecord()->name;
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
                ->modalHeading(__('nomenclature.headings.inactivate_service_modal'))
                ->modalDescription(__('nomenclature.helper_texts.inactivate_service_modal'))
                ->modalSubmitActionLabel(__('nomenclature.actions.change_status.inactivate_service_modal'))
                ->action(fn () => $record->update(['status' => GeneralStatus::INACTIVE]))
                ->successNotificationTitle(__('nomenclature.actions.change_status.inactivate'))
                ->after(fn () => $this->redirect(ServiceResource::getUrl('view', ['record' => $record])));
        } else {
            $actions[] = Action::make('activate')
                ->label(__('nomenclature.actions.change_status.activate'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(fn () => $record->update(['status' => GeneralStatus::ACTIVE]))
                ->successNotificationTitle(__('nomenclature.actions.change_status.activate'))
                ->after(fn () => $this->redirect(ServiceResource::getUrl('view', ['record' => $record])));
        }

        return $actions;
    }
}
