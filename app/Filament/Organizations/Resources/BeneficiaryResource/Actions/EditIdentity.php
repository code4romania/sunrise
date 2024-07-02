<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Actions;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Infolists\Components\Actions\Action;

class EditIdentity extends Action
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->url(fn ($record) => BeneficiaryResource::getUrl('edit_identity', ['record' => $record]));
        $this->label(__('general.action.edit'));
        $this->link();
    }
}
