<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Actions;

use App\Enums\CaseStatus;
use Filament\Actions\Action;

class ChangeStatus extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('beneficiary.action.' . $this->getName()));
        $status = CaseStatus::tryFrom($this->getName());

        $this->disabled(function ($record) use ($status) {
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

        $this->action(fn ($record) => $record->update(['status' => $status]));
    }
}
