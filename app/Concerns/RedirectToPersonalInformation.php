<?php

declare(strict_types=1);

namespace App\Concerns;

trait RedirectToPersonalInformation
{
    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl('view_personal_information', [
            'record' => $this->record->id,
            'tab' => sprintf('-%s-tab', $this->getTabSlug()),
        ]);
    }
}
