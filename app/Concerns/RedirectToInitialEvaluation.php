<?php

declare(strict_types=1);

namespace App\Concerns;

trait RedirectToInitialEvaluation
{
    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl('view_initial_evaluation', [
            'record' => $this->record->id,
            'tab' => sprintf('-%s-tab', $this->getTabSlug()),
        ]);
    }
}
