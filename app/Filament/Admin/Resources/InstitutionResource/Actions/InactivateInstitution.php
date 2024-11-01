<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Actions;

use App\Models\Institution;
use Filament\Actions\Action;

class InactivateInstitution extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('inactivate_institution');
        $this->label(__('institution.actions.inactivate'));
        $this->icon('heroicon-o-user-minus');
        $this->color('danger');
        $this->outlined();
        $this->visible(fn (Institution $record) => $record->isActivated());
        $this->modalHeading(__('institution.headings.inactivate'));
        $this->modalDescription(__('institution.labels.inactivate'));
        $this->modalSubmitActionLabel(__('institution.actions.inactivate'));
        $this->action(fn (Institution $record) => $record->inactivate());
    }
}
