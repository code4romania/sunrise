<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonthlyPlanResource\Pages;

use App\Actions\BackAction;
use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\MonthlyPlanResource;
use App\Infolists\Components\SectionHeader;
use App\Infolists\Components\TableEntry;
use App\Models\Specialist;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMonthlyPlan extends ViewRecord
{
    use HasParentResource;

    protected static string $resource = MonthlyPlanResource::class;

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->parent)
            ->getViewMonthlyPlan($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.monthly_plan');
    }

    public function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view_intervention_plan', [
                    'parent' => $this->parent->beneficiary,
                    'record' => $this->parent,
                ])),

            DeleteAction::make()
                ->label(__('intervention_plan.actions.delete_monthly_plan'))
                ->icon('heroicon-o-trash')
                ->modalHeading(__('intervention_plan.headings.delete_monthly_plan_modal'))
                ->modalSubmitActionLabel(__('intervention_plan.actions.delete_monthly_plan'))
                ->successRedirectUrl(
                    BeneficiaryResource::getUrl('view_intervention_plan', [
                        'parent' => $this->parent->beneficiary,
                        'record' => $this->parent,
                    ])
                )
                ->outlined(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $this->getRecord()
            ->load([
                'monthlyPlanServices.service',
                'monthlyPlanServices.monthlyPlanInterventions.serviceIntervention',
            ]);

        return $infolist->schema([
            Tabs::make()
                ->persistTabInQueryString()
                ->schema([
                    Tab::make(__('intervention_plan.headings.monthly_plan_details'))
                        ->schema([
                            Section::make()
                                ->maxWidth('3xl')
                                ->columns()
                                ->schema([
                                    SectionHeader::make('monthly_plan_details')
                                        ->state(__('intervention_plan.headings.monthly_plan_details'))
                                        ->action(
                                            Action::make('edit_details')
                                                ->label(__('general.action.edit'))
                                                ->link()
                                                ->url(
                                                    InterventionPlanResource::getUrl('edit_monthly_plan_details', [
                                                        'parent' => $this->parent,
                                                        'record' => $this->getRecord(),
                                                    ])
                                                )
                                        ),

                                    TextEntry::make('beneficiary.full_name')
                                        ->label(__('intervention_plan.labels.full_name')),

                                    TextEntry::make('beneficiary.organization_beneficiary_id')
                                        ->label(__('intervention_plan.labels.organization_beneficiary_id')),

                                    TextEntry::make('start_date')
                                        ->label(__('intervention_plan.labels.plan_date'))
                                        ->date('d.m.Y'),

                                    TextEntry::make('interval')
                                        ->label(__('intervention_plan.labels.interval')),

                                    TextEntry::make('caseManager.full_name')
                                        ->label(__('intervention_plan.labels.case_manager')),

                                    TextEntry::make('specialists')
                                        ->label(__('intervention_plan.labels.specialists'))
                                        ->formatStateUsing(
                                            fn (string $state) => collect(explode(',', $state))
                                                ->map(
                                                    fn (string $specialistID) => Specialist::find((int) (trim($specialistID)))
                                                        ->name_role
                                                )
                                                ->implode(', ')
                                        )
                                        ->listWithLineBreaks(),
                                ]),

                        ]),

                    Tab::make(__('intervention_plan.headings.services_and_interventions'))
                        ->schema([
                            Section::make()
                                ->maxWidth('3xl')
                                ->schema([
                                    SectionHeader::make('monthly_plan_details')
                                        ->state(__('intervention_plan.headings.services_and_interventions'))
                                        ->action(
                                            Action::make('edit_details')
                                                ->label(__('general.action.edit'))
                                                ->link()
                                                ->url(
                                                    InterventionPlanResource::getUrl('edit_monthly_plan_services_and_interventions', [
                                                        'parent' => $this->parent,
                                                        'record' => $this->getRecord(),
                                                    ])
                                                )
                                        ),

                                    RepeatableEntry::make('monthlyPlanServices')
                                        ->hiddenLabel()
                                        ->columns()
                                        ->columnSpanFull()
                                        ->schema([
                                            SectionHeader::make('service.name'),

                                            TextEntry::make('institution')
                                                ->label(__('intervention_plan.labels.responsible_institution')),

                                            TextEntry::make('responsible_person')
                                                ->label(__('intervention_plan.labels.responsible_person')),

                                            TextEntry::make('start_date')
                                                ->label(__('intervention_plan.labels.monthly_plan_service_interval_start'))
                                                ->formatStateUsing(
                                                    fn ($state) => $state === '-' ? $state : Carbon::parse($state)->format('d.m.Y')
                                                ),

                                            TextEntry::make('end_date')
                                                ->label(__('intervention_plan.labels.monthly_plan_service_interval_end'))
                                                ->formatStateUsing(
                                                    fn ($state) => $state === '-' ? $state : Carbon::parse($state)->format('d.m.Y')
                                                ),

                                            TextEntry::make('objective')
                                                ->label(__('intervention_plan.labels.service_objective')),

                                            TableEntry::make('monthlyPlanInterventions')
                                                ->columnSpanFull()
                                                ->hiddenLabel()
                                                ->schema([
                                                    TextEntry::make('serviceIntervention.name')
                                                        ->label(__('intervention_plan.headings.interventions'))
                                                        ->hiddenLabel(),

                                                    TextEntry::make('objections')
                                                        ->label(__('intervention_plan.labels.objections'))
                                                        ->hiddenLabel(),

                                                    TextEntry::make('observations')
                                                        ->label(__('intervention_plan.labels.observations'))
                                                        ->hiddenLabel(),
                                                ]),

                                            TextEntry::make('service_details')
                                                ->label(__('intervention_plan.labels.service_details'))
                                                ->columnSpanFull(),
                                        ]),
                                ]),

                        ]),
                ]),

        ]);
    }
}
