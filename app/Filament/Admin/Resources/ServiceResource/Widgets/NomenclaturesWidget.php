<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Widgets;

use App\Filament\Admin\Resources\BenefitResource\Widgets\BenefitWidget;
use Kenepa\MultiWidget\MultiWidget;

class NomenclaturesWidget extends MultiWidget
{
    public array $widgets = [
        ServiceWidget::class,
        BenefitWidget::class,

    ];

    public function shouldPersistMultiWidgetTabsInSession(): bool
    {
        return true;
    }
}
