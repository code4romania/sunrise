<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Widgets;

use Kenepa\MultiWidget\MultiWidget;

class ServiceWidget extends MultiWidget
{
    public array $widgets = [
        InterventionsWidget::class,
        CounselingSheetWidget::class,
        ServiceDescriptionWidget::class,
    ];

    public function shouldPersistMultiWidgetTabsInSession(): bool
    {
        return true;
    }
}
