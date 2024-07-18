<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Actions;

use Filament\Infolists\Components\Actions\Action;

class Edit extends Action
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->label(__('general.action.edit'));
        $this->link();
    }
}
