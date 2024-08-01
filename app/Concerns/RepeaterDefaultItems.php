<?php

declare(strict_types=1);

namespace App\Concerns;

use Filament\Forms\Components\Actions\Action;

trait RepeaterDefaultItems
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->deletable(function (?array $state) {
            if ($this->getMinItems() === null) {
                return true;
            }

            return collect($state)->count() > $this->getMinItems();
        });

        $this->deleteAction(fn (Action $action) => $action->requiresConfirmation());
    }

    public function fillFromRelationship(): void
    {
        parent::fillFromRelationship();

        $this->ensureMinItems();
    }

    public function ensureMinItems(): void
    {
        $count = collect($this->getState())->count();

        $minItems = $this->getMinItems() ?? 1;

        while ($count < $minItems) {
            $this->createItem();

            $count++;
        }
    }

    protected function createItem(): void
    {
        $newUuid = $this->generateUuid();

        $items = $this->getState();

        if ($newUuid) {
            $items[$newUuid] = [];
        } else {
            $items[] = [];
        }

        $this->state($items);

        $this->getChildComponentContainer($newUuid ?? array_key_last($items))->fill();

        $this->collapsed(false, shouldMakeComponentCollapsible: false);
    }
}
