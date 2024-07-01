<?php

declare(strict_types=1);

namespace App\Concerns;

trait RedirectToDetailedEvaluation
{
    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl('view_detailed_evaluation', ['record' => $this->record->id]) .
            '?tab=-' . $this->getTabSlug() . '-tab';
    }
}
