<?php

declare(strict_types=1);

namespace App\Concerns;

trait RedirectToMonitoring
{
    use HasBackAction;

    protected function getRedirectUrl(): ?string
    {
        return self::getParentResource()::getUrl('monitorings.view', [
            'parent' => $this->getRecord()->beneficiary,
            'record' => $this->getRecord(),
            'tab' => \sprintf('-%s-tab', $this->getTabSlug()),
        ]);
    }
}
