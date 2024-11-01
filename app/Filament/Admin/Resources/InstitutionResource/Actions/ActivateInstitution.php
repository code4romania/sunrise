<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Actions;

use App\Models\Institution;
use Filament\Actions\Action;

class ActivateInstitution extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('activate_institution');
        $this->label(__('institution.actions.activate'));
        $this->icon('heroicon-s-arrow-path');
        $this->color('success');
        $this->outlined();
        $this->visible(fn (Institution $record) => $record->isInactivated());
        $this->action(fn (Institution $record) => $record->activate());
    }
}
