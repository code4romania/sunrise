<?php

declare(strict_types=1);

namespace App\Widgets;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class FormWidget extends InfolistWidget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'widgets.form-widget';
}
