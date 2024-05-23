<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ListSpecialists extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            BeneficiaryResource\Widgets\CaseTeam::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 1;
    }

    public function getHeading(): string|Htmlable
    {
        return __('beneficiary.section.specialists.title');
    }
}
