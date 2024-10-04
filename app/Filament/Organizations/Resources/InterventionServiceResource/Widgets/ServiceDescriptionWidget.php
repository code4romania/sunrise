<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Widgets;

use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Models\InterventionService;
use App\Widgets\InfolistWidget;
use Filament\Infolists\Components\Actions\Action;
//use Filament\Actions\Action;
use Filament\Infolists\Components\Section;
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
                ->headerActions([
                    Action::make('edit')
                        ->label(__('intervention_plan.actions.edit'))
                        ->icon('heroicon-o-pencil')
                        ->link()
                        ->url(InterventionPlanResource::getUrl('edit_intervention_service', [
                            'parent' => $this->record->interventionPlan,
                            'record' => $this->record,
                        ])),
                ])
                ->schema([
                    TextEntry::make('institution')
                        ->label(__('intervention_plan.labels.responsible_institution')),
                    TextEntry::make('user.full_name')
                        ->label(__('intervention_plan.labels.responsible_specialist')),
                    TextEntry::make('start_date')
                        ->label(__('intervention_plan.labels.start_date')),
                    TextEntry::make('end_date')
                        ->label(__('intervention_plan.labels.end_date')),
                    TextEntry::make('objections')
                        ->label(__('intervention_plan.labels.objections'))
                        ->columnSpanFull()
                        ->html(),
                ]),
        ];
    }
}
