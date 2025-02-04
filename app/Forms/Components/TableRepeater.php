<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Concerns\RepeaterDefaultItems;
use Awcodes\TableRepeater\Components\TableRepeater as BaseTableRepeater;

class TableRepeater extends BaseTableRepeater
{
    use RepeaterDefaultItems;
}
