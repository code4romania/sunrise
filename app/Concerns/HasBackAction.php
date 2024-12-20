<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Actions\BackAction;

trait HasBackAction
{
    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url($this->getRedirectUrl()),
        ];
    }
}
