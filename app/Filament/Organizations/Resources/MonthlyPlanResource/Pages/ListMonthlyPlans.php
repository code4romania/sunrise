<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonthlyPlanResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Organizations\Resources\MonthlyPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonthlyPlans extends ListRecords
{
    protected static string $resource = MonthlyPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
