<?php

declare(strict_types=1);

namespace App\Filament\Admin\Actions;

use App\Enums\GeneralStatus;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;

class ChangeNomenclatureStatusAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'change-status';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn ($record) => GeneralStatus::isValue($record->status, GeneralStatus::ACTIVE) ?
            __('nomenclature.actions.change_status.inactivate') :
            __('nomenclature.actions.change_status.activate'));

        $this->color(fn ($record) => GeneralStatus::isValue($record->status, GeneralStatus::ACTIVE) ? (GeneralStatus::INACTIVE->getColor()) : GeneralStatus::ACTIVE->getColor());

        $this->action(fn ($record) => $record->update(['status' => ! $record->status->value]));

        $recordClass = get_class($this->getRecord());
        $recordClass = strtolower( substr($recordClass, strrpos($recordClass, '\\')+1));
        $modalLabelKey = sprintf('inactivate_%s_modal', $recordClass);

        $this->modalHeading(fn ($record) => GeneralStatus::isValue($record->status, GeneralStatus::ACTIVE) ? __('nomenclature.headings.' . $modalLabelKey) : null);
        $this->modalDescription(fn ($record) => GeneralStatus::isValue($record->status, GeneralStatus::ACTIVE) ? __('nomenclature.helper_texts.' . $modalLabelKey) : null);
        $this->modalSubmitActionLabel(fn ($record) => GeneralStatus::isValue($record->status, GeneralStatus::ACTIVE) ? __('nomenclature.actions.change_status.' . $modalLabelKey) : null);

        $this->modalFooterActionsAlignment(Alignment::Right);
    }
}
