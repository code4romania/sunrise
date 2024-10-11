<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListBeneficiaries extends ListRecords
{
    protected static string $resource = BeneficiaryResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('beneficiary.action.create')),
        ];
    }

    public function table(Table $table): Table
    {
        activity()->disableLogging();

        return parent::table($table);
    }
}
