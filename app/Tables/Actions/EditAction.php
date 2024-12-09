<?php

declare(strict_types=1);

namespace App\Tables\Actions;

use App\Concerns\HasModalExtraFooterActionsAlignment;
use Filament\Tables\Actions\EditAction as BaseEditAction;

class EditAction extends BaseEditAction
{
    use HasModalExtraFooterActionsAlignment;
}
