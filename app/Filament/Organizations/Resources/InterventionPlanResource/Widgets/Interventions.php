<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Widgets;

use Kenepa\MultiWidget\MultiWidget;

class Interventions extends MultiWidget
{
    public array $widgets = [
        ServicesWidget::class,
        //        BenefitsWidget::class,
    ];
}
