<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Pages;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInterventionPlan extends CreateRecord
{
    use PreventSubmitFormOnEnter;
    use PreventSubmitFormOnEnter;

    protected static string $resource = InterventionPlanResource::class;
}
