<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Services\Schemas;

use App\Models\BeneficiaryIntervention;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    TextEntry::make('serviceWithoutStatusCondition.name')
                        ->label(__('service.labels.name')),
                    TextEntry::make('status')
                        ->label(__('service.labels.status'))
                        ->badge(),
                ]),

            Section::make(__('service.headings.interventions'))
                ->schema([
                    RepeatableEntry::make('interventions')
                        ->columnSpanFull()
                        ->columns(2)
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('serviceInterventionWithoutStatusCondition.name')
                                ->label(__('service.labels.interventions')),

                            TextEntry::make('cases_count')
                                ->label(__('service.labels.cases'))
                                ->state(function ($record) {
                                    if (! $record->relationLoaded('beneficiaryInterventions')) {
                                        $record->load('beneficiaryInterventions.interventionPlan');
                                    }

                                    return $record->beneficiaryInterventions
                                        ?->map(fn (BeneficiaryIntervention $bi) => $bi->interventionPlan?->beneficiary_id)
                                        ->unique()
                                        ->filter()
                                        ->count() ?? 0;
                                }),

                            TextEntry::make('status')
                                ->label(__('service.labels.status'))
                                ->badge(),
                        ]),
                ]),
        ]);
    }
}
