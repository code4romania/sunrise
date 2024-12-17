<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonthlyPlanResource\Pages;

use App\Filament\Organizations\Resources\MonthlyPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonthlyPlan extends EditRecord
{
    protected static string $resource = MonthlyPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
