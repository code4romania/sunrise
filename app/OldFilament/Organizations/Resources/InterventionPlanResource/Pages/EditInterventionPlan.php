<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Pages;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInterventionPlan extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = InterventionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
