<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Actions;

use Filament\Infolists\Components\Actions\Action;
use Filament\Support\Enums\ActionSize;

class EditExtraLarge extends Action
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->size(ActionSize::ExtraLarge);
    }
}
