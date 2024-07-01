<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Actions;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Infolists\Components\Actions\Action;

class EditPersonalInformation extends Action
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->url(fn ($record) => BeneficiaryResource::getUrl('edit_personal_information', ['record' => $record]));
        $this->label(__('general.action.edit'));
        $this->link();
    }
}
