<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBeneficiaryInterventions extends ListRecords
{
    protected static string $resource = BeneficiaryInterventionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
