<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Results\Pages;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\Results\ResultResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditResult extends EditRecord
{
    protected static string $resource = ResultResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list').' - '.__('nomenclature.headings.results').': '.$this->getRecord()->name;
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        $canDelete = $record->interventionPlanResults()->count() === 0;

        $actions = [];

        if ($record->status === GeneralStatus::ACTIVE) {
            $actions[] = Action::make('inactivate')
                ->label(__('nomenclature.actions.change_status.inactivate'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('nomenclature.headings.inactivate_result_modal'))
                ->modalDescription(__('nomenclature.helper_texts.inactivate_result_modal'))
                ->modalSubmitActionLabel(__('nomenclature.actions.change_status.inactivate_result_modal'))
                ->action(fn () => $record->update(['status' => GeneralStatus::INACTIVE]))
                ->successNotificationTitle(__('nomenclature.actions.change_status.inactivate'))
                ->after(fn () => $this->redirect(ResultResource::getUrl('edit', ['record' => $record])));
        } else {
            $actions[] = Action::make('activate')
                ->label(__('nomenclature.actions.change_status.activate'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(fn () => $record->update(['status' => GeneralStatus::ACTIVE]))
                ->successNotificationTitle(__('nomenclature.actions.change_status.activate'))
                ->after(fn () => $this->redirect(ResultResource::getUrl('edit', ['record' => $record])));
        }

        $actions[] = DeleteAction::make()
            ->label(__('nomenclature.actions.delete_result'))
            ->modalHeading(__('nomenclature.headings.delete_result_modal_heading'))
            ->disabled(! $canDelete)
            ->successRedirectUrl(ResultResource::getUrl());

        return $actions;
    }
}
