<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserInstitutionResource\Actions;

use App\Models\User;
use Filament\Actions\Action;

class ActivateUserAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'activate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (User $record) => $record->isInactive());

        $this->label(__('user.actions.activate'));

        $this->color('success');

        $this->outlined();

        $this->icon('heroicon-o-arrow-path');

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $record->activate();
            $this->success();
        });

    }
}
