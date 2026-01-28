<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages;

use Filament\Schemas\Schema;
use App\Actions\BackAction;
use App\Concerns\HasGroupPages;
use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Infolists\Components\Actions\EditAction;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBeneficiaryIntervention extends ViewRecord
{
    use HasParentResource;
    use HasGroupPages;

    protected static string $resource = BeneficiaryInterventionResource::class;

    protected string $view = 'filament.organizations.pages.view-beneficiary-interventions';

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->getRecord()->interventionService->interventionPlan)
            ->getInterventionBreadcrumb($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->organizationServiceIntervention->serviceInterventionWithoutStatusCondition->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(
                    InterventionPlanResource::getUrl('view_intervention_service', [
                        'parent' => $this->parent->interventionPlan,
                        'record' => $this->parent,
                    ])
                ),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                Section::make(__('intervention_plan.headings.intervention_indicators'))
                    ->headerActions([
                        EditAction::make('edit_intervention')
                            ->url(fn () => InterventionServiceResource::getUrl('edit_intervention', [
                                'parent' => $this->record->interventionService,
                                'record' => $this->record,
                            ])),
                    ])
                    ->maxWidth('3xl')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('organizationServiceIntervention.serviceIntervention.name')
                                    ->label(__('intervention_plan.labels.intervention_type')),

                                TextEntry::make('specialist.name_role')
                                    ->label(__('intervention_plan.labels.responsible_specialist')),

                                TextEntry::make('interval')
                                    ->label(__('intervention_plan.labels.start_date_interval')),
                            ]),

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
