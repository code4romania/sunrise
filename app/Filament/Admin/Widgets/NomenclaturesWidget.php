<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\BenefitResource\Widgets\BenefitWidget;
use App\Filament\Admin\Resources\ServiceResource\Widgets\ServiceWidget;
use Kenepa\MultiWidget\MultiWidget;

class NomenclaturesWidget extends MultiWidget
{
    public array $widgets = [
        ServiceWidget::class,
//        BenefitWidget::class,

    ];

    public function shouldPersistMultiWidgetTabsInSession(): bool
    {
        return true;
    }
}
