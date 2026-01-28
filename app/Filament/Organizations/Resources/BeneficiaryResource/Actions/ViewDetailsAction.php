<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Actions;

use Filament\Infolists\Components\Actions\Action;

class ViewDetailsAction extends \Filament\Actions\Action
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->label(__('general.action.view_details'));
        $this->link();
    }
}
