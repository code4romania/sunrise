<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Actions;

use App\Enums\CaseStatus;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ChangeStatus extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('beneficiary.action.' . $this->getName()));
        $status = CaseStatus::tryFrom($this->getName());

        $this->disabled(function (Beneficiary $record) use ($status) {
            if ($record->status === $status) {
                return true;
            }

            if (CaseStatus::isValue($record->status, CaseStatus::ACTIVE) &&
                $status === CaseStatus::ARCHIVED) {
                return true;
            }

            if (CaseStatus::isValue($record->status, CaseStatus::MONITORED) &&
                $status === CaseStatus::ARCHIVED) {
                return true;
            }

            if (CaseStatus::isValue($record->status, CaseStatus::CLOSED) &&
                $status === CaseStatus::MONITORED) {
                return true;
            }

            if (CaseStatus::isValue($record->status, CaseStatus::ARCHIVED) &&
                ($status === CaseStatus::MONITORED || $status === CaseStatus::ACTIVE)) {
                return true;
            }

            return false;
        });

        $this->action(function ($record) use ($status) {
            $record->update(['status' => $status]);
            Notification::make()
                ->title(__('beneficiary.notification.change_status.title'))
                ->success()
                ->body(__('beneficiary.notification.change_status.body', [
                    'status' => $status->getLabel(),
                ]))->send();

            if (CaseStatus::isValue($record->status, CaseStatus::CLOSED)) {
                $this->redirect(BeneficiaryResource::getUrl('view', ['record' => $record]));
            }
        });
    }
}
