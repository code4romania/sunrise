<?php

declare(strict_types=1);

namespace App\Concerns;

trait RedirectToMonitoring
{
    use HasBackAction;

    protected function getRedirectUrl(): ?string
    {
        $parentRecord = $this->getParentRecord();

        return static::getResource()::getUrl('view', [
            'beneficiary' => $parentRecord,
            'record' => $this->getRecord(),
            'tab' => \sprintf('-%s-tab', $this->getTabSlug()),
        ]);
    }
}
