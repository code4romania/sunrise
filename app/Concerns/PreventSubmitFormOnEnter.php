<?php

declare(strict_types=1);

namespace App\Concerns;

use Filament\Actions\Action;

trait PreventSubmitFormOnEnter
{
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->extraAttributes(['type' => 'button']);
    }
}
