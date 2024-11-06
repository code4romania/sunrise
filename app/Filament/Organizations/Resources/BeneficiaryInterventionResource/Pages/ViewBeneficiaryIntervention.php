<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages;

use App\Concerns\HasGroupPages;
use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBeneficiaryIntervention extends ViewRecord
{
    use HasParentResource;
    use HasGroupPages;

    protected static string $resource = BeneficiaryInterventionResource::class;

    protected static string $view = 'filament.organizations.pages.view-beneficiary-interventions';

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->getRecord()->interventionService->interventionPlan)
            ->getInterventionBreadcrumb($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->organizationServiceIntervention->serviceInterventionWithoutStatusCondition->name;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('intervention_plan.headings.intervention_indicators'))
                    ->headerActions([
                        Action::make('edit_intervention')
                            ->label(__('general.action.edit'))
                            ->icon('heroicon-o-pencil')
                            ->link()
                            ->url(fn () => InterventionServiceResource::getUrl('edit_intervention', [
                                'parent' => $this->record->interventionService,
                                'record' => $this->record,
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

            ]);
    }
}
