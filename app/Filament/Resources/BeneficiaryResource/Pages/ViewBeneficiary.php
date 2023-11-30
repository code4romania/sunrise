<?php

declare(strict_types=1);

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Filament\Resources\BeneficiaryResource;
use Filament\Resources\Pages\ViewRecord;

class ViewBeneficiary extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
