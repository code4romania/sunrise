<?php

declare(strict_types=1);

namespace App\Filament\Schemas\Components;

use Filament\Actions\Action;
use Filament\Schemas\Components\Section;

/**
 * Section that sets the component record on header/footer actions during prepareAction
 * so getRecord() does not traverse the schema tree (avoids memory exhaustion).
 */
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
