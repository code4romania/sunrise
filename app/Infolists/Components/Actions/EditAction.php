<?php

declare(strict_types=1);

namespace App\Infolists\Components\Actions;

use App\Concerns\HasModalExtraFooterActionsAlignment;

class EditAction extends \Filament\Actions\Action
{
    use HasModalExtraFooterActionsAlignment;

    public static function getDefaultName(): ?string
    {
        return 'edit';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('general.action.edit'));
        $this->icon('heroicon-o-pencil-square');

        $this->link();
    }
}
