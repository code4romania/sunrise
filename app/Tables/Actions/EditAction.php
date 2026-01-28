<?php

declare(strict_types=1);

namespace App\Tables\Actions;

use App\Concerns\HasModalExtraFooterActionsAlignment;
use Filament\Tables\Actions\EditAction as BaseEditAction;

class EditAction extends \Filament\Actions\EditAction
{
    use HasModalExtraFooterActionsAlignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('general.action.change'));
        $this->icon(null);
    }
}
