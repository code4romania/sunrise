<?php

declare(strict_types=1);

namespace App\Concerns;

trait HasGroupPages
{
    public function getGroupPages(): array
    {
        return self::$resource::getGroupPages($this->parent, $this->getRecord());
    }
}
