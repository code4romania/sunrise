<?php

declare(strict_types=1);

namespace App\Concerns;

trait RedirectToIdentity
{
    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl('view_identity', [
            'record' => $this->record->id,
            'tab' => sprintf('-%s-tab', $this->getTabSlug()),
        ]);
    }
}
