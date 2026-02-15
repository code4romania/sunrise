<?php

declare(strict_types=1);

namespace App\Filament\Schemas\Components;

use Filament\Actions\Action;
use Filament\Schemas\Components\Section;

class SectionWithRecordActions extends Section
{
    public function prepareAction(Action $action): Action
    {
        $record = $this->getRecord();
        if ($record !== null) {
            $action->record($record);
        }

        return parent::prepareAction($action);
    }
}
