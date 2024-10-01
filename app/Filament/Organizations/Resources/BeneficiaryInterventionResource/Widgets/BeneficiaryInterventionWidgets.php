<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Widgets;

use Kenepa\MultiWidget\MultiWidget;

class BeneficiaryInterventionWidgets extends MultiWidget
{
    public array $widgets = [
        MeetingWidget::class,
        InterventionWidget::class,
    ];
}
