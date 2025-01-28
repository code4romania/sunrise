<?php

declare(strict_types=1);

namespace App\Actions;

use Filament\Actions\Action;

class BackAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-chevron-left');
        $this->hiddenLabel();
        $this->link();
        $this->extraAttributes([
            'class' => 'p-4'
        ]);
    }

    public function getName(): ?string
    {
        return 'back_action';
    }
}
