<?php

namespace App\Filament\Organizations\Resources\ServiceResource\Actions;

use App\Enums\GeneralStatus;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;

class ChangeStatusAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'change-status';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn ($record) => GeneralStatus::isValue($record->status, GeneralStatus::ACTIVE) ?
            __('service.actions.change_status.inactivate') :
            __('service.actions.change_status.activate'));

        $this->color(fn ($record) => GeneralStatus::isValue($record->status, GeneralStatus::ACTIVE) ? (GeneralStatus::INACTIVE->getColor()) : GeneralStatus::ACTIVE->getColor());

        $this->action(fn ($record) => $record->update(['status' => ! $record->status->value]));

        $this->modalHeading(fn ($record) => GeneralStatus::isValue($record->status, GeneralStatus::ACTIVE) ? __('nomenclature.headings.inactivate_modal') : null);
        $this->modalDescription(fn ($record) => GeneralStatus::isValue($record->status, GeneralStatus::ACTIVE) ? __('nomenclature.helper_texts.inactivate_modal') : null);
        $this->modalSubmitActionLabel(fn ($record) => GeneralStatus::isValue($record->status, GeneralStatus::ACTIVE) ? __('nomenclature.actions.change_status.inactivate_modal') : null);

        $this->modalFooterActionsAlignment(Alignment::Right);
    }
}
