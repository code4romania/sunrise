<?php

declare(strict_types=1);

namespace App\Concerns;

trait RedirectToCloseFile
{
    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl('view_close_file', [
            'record' => $this->getRecord(),
            'tab' => \sprintf('-%s-tab', $this->getTabSlug()),
        ]);
    }
}
