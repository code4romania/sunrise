<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Concerns\RepeaterDefaultItems;
use Filament\Forms\Components\Repeater as BaseRepeater;

class Repeater extends BaseRepeater
{
    use RepeaterDefaultItems;
}
