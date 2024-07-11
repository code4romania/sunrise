<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Awcodes\FilamentTableRepeater\Components\TableRepeater as BaseTableRepeater;

class TableRepeater extends BaseTableRepeater
{
    public function fillFromRelationship(): void
    {
        parent::fillFromRelationship();

        $this->ensureMinItems();
    }

    public function ensureMinItems(): void
    {
        $count = $this->getMinItems()
            ? collect($this->getState())->count()
            : 1;

        $minItems = $this->getMinItems() ?? 1;

        while ($count < $minItems) {
            $this->createItem();

            $count++;
        }
    }

    protected function createItem(): void
    {
        debug('createItem');
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
