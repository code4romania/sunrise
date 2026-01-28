<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Widgets;

use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Infolists\Components\Actions\EditAction;
use App\Models\InterventionService;
use App\Widgets\InfolistWidget;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ServiceDescriptionWidget extends InfolistWidget
{
    public ?InterventionService $record = null;

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.service_description');
    }

    protected function getInfolistSchema(): array
    {
        return [
            Section::make(__('intervention_plan.headings.service_description'))
                ->maxWidth('3xl')
                ->columns()
                ->compact()
                ->headerActions([
                    EditAction::make()
                        ->url(InterventionPlanResource::getUrl('edit_intervention_service', [
                            'parent' => $this->record->interventionPlan,
                            'record' => $this->record,
                        ])),
                ])
                ->schema([
                    TextEntry::make('institution')
                        ->label(__('intervention_plan.labels.responsible_institution')),

                    TextEntry::make('specialist.name_role')
                        ->label(__('intervention_plan.labels.responsible_specialist')),

                    TextEntry::make('interval')
                        ->label(__('intervention_plan.labels.interval')),

                    TextEntry::make('objections')
                        ->label(__('intervention_plan.labels.objections'))
                        ->columnSpanFull()
                        ->html(),
                ]),
        ];
    }
}
