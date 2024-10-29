<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\ServiceResource\Widgets;

use App\Models\BeneficiaryIntervention;
use App\Models\OrganizationService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ListServiceInterventions extends BaseWidget
{
    public ?OrganizationService $record = null;

    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record->interventions()
                    ->with([
                        'serviceInterventionWithoutStatusCondition',
                        'beneficiaryInterventions.interventionPlan',
                    ])
            )
            ->columns([
                TextColumn::make('serviceInterventionWithoutStatusCondition.name')
                    ->label(__('service.labels.interventions')),

                TextColumn::make('cases')
                    ->label(__('service.labels.cases'))
                    ->default(
                        fn ($record) => $record->beneficiaryInterventions
                            ?->map(fn (BeneficiaryIntervention $beneficiaryIntervention) => $beneficiaryIntervention->interventionPlan->beneficiary_id)
                            ->unique()
                            ->count()
                    ),

                TextColumn::make('status')
                    ->label(__('service.labels.status')),
            ])
            ->heading(null);
    }
}
