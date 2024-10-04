<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Widgets;

use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Models\BeneficiaryIntervention;
use App\Widgets\InfolistWidget;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class InterventionWidget extends InfolistWidget
{
    public ?BeneficiaryIntervention $record = null;

    public function getInfolistSchema(): array
    {
        return [
            Section::make(__('intervention_plan.headings.intervention_indicators'))
                ->headerActions([
                    Action::make('edit_intervention')
                        ->label(__('general.action.edit'))
                        ->icon('heroicon-o-pencil')
                        ->outlined()
                        ->url(fn ($record) => InterventionServiceResource::getUrl('edit_intervention', [
                            'parent' => $this->record,
                            'record' => $record,
                        ])),
                ])
                ->maxWidth('3xl')
                ->schema([
                    TextEntry::make('objections')
                        ->label(__('intervention_plan.labels.objections')),
                    TextEntry::make('expected_results')
                        ->label(__('intervention_plan.labels.expected_results')),
                    TextEntry::make('procedure')
                        ->label(__('intervention_plan.labels.procedure')),
                    TextEntry::make('indicators')
                        ->label(__('intervention_plan.labels.indicators')),
                    TextEntry::make('achievement_degree')
                        ->label(__('intervention_plan.labels.achievement_degree')),
                ]),
        ];
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.intervention_indicators');
    }
}
