<?php

declare(strict_types=1);

namespace App\Concerns;

trait RepeaterDefaultItems
{
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
