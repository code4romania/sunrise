<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\SectionHeader;
use App\Models\Beneficiary;
use App\Models\MonthlyPlan;
use App\Models\Specialist;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ViewCaseMonthlyPlan extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    protected ?MonthlyPlan $monthlyPlan = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        if (! $this->record instanceof Beneficiary) {
            abort(404);
        }

        $plan = $this->record->interventionPlan;
        if (! $plan) {
            $this->redirect(CaseResource::getUrl('view_intervention_plan', ['record' => $this->record]));

            return;
        }

        $monthlyPlanId = request()->route('monthlyPlan');
        $this->monthlyPlan = MonthlyPlan::query()
            ->where('intervention_plan_id', $plan->id)
            ->where('id', $monthlyPlanId)
            ->with([
                'monthlyPlanServices.service',
                'monthlyPlanServices.monthlyPlanInterventions.serviceIntervention',
                'interventionPlan.beneficiary',
            ])
            ->firstOrFail();

        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(CaseResource::canView($this->record), 403);
    }

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.monthly_plan');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            CaseResource::getUrl('view_intervention_plan', ['record' => $record]) => __('intervention_plan.headings.view_page'),
            '' => __('intervention_plan.headings.monthly_plan'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view_intervention_plan', ['record' => $this->getRecord()])),
            DeleteAction::make()
                ->label(__('intervention_plan.actions.delete_monthly_plan'))
                ->modalHeading(__('intervention_plan.headings.delete_monthly_plan_modal'))
                ->modalDescription(__('intervention_plan.labels.delete_monthly_plan_modal_description'))
                ->modalSubmitActionLabel(__('intervention_plan.actions.delete_monthly_plan'))
                ->record($this->monthlyPlan)
                ->successRedirectUrl(CaseResource::getUrl('view_intervention_plan', ['record' => $this->getRecord()]))
                ->outlined(),
        ];
    }

    public function defaultInfolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->inlineLabel(true)
            ->record($this->monthlyPlan);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->persistTabInQueryString()
                ->schema([
                    Tab::make(__('intervention_plan.headings.monthly_plan_details'))
                        ->schema([
                            Section::make()
                                ->maxWidth('3xl')
                                ->columns(2)
                                ->schema([
                                    SectionHeader::make('monthly_plan_details')
                                        ->state(__('intervention_plan.headings.monthly_plan_details'))
                                        ->action(
                                            EditAction::make()
                                                ->url(
                                                    CaseResource::getUrl('edit_monthly_plan_details', [
                                                        'record' => $this->getRecord(),
                                                        'monthlyPlan' => $this->monthlyPlan,
                                                    ])
                                                )
                                        ),

                                    TextEntry::make('beneficiary.full_name')
                                        ->label(__('intervention_plan.labels.full_name')),

                                    TextEntry::make('beneficiary.organization_beneficiary_id')
                                        ->label(__('intervention_plan.labels.organization_beneficiary_id')),

                                    TextEntry::make('start_date')
                                        ->label(__('intervention_plan.labels.plan_date'))
                                        ->date('Y-m-d'),

                                    TextEntry::make('interval')
                                        ->label(__('intervention_plan.labels.interval')),

                                    TextEntry::make('caseManager.full_name')
                                        ->label(__('intervention_plan.labels.case_manager')),

                                    TextEntry::make('specialists')
                                        ->label(__('intervention_plan.labels.specialists'))
                                        ->formatStateUsing(function ($state): string {
                                            if (blank($state) || ! is_iterable($state)) {
                                                return '—';
                                            }
                                            $ids = is_array($state) ? $state : ($state instanceof \Illuminate\Support\Collection ? $state->all() : iterator_to_array($state));

                                            return Specialist::query()
                                                ->whereIn('id', $ids)
                                                ->with(['user', 'roleForDisplay'])
                                                ->get()
                                                ->pluck('name_role')
                                                ->implode(', ');
                                        }),
                                ]),
                        ]),

                    Tab::make(__('intervention_plan.headings.services_and_interventions'))
                        ->schema([
                            Section::make()
                                ->maxWidth('3xl')
                                ->schema([
                                    SectionHeader::make('services_header')
                                        ->state(__('intervention_plan.headings.services_and_interventions')),

                                    RepeatableEntry::make('monthlyPlanServices')
                                        ->hiddenLabel()
                                        ->columns(2)
                                        ->columnSpanFull()
                                        ->schema([
                                            TextEntry::make('service.name')
                                                ->label(__('intervention_plan.headings.service')),

                                            TextEntry::make('institution')
                                                ->label(__('intervention_plan.labels.responsible_institution')),

                                            TextEntry::make('responsible_person')
                                                ->label(__('intervention_plan.labels.responsible_person')),

                                            TextEntry::make('start_date')
                                                ->label(__('intervention_plan.labels.monthly_plan_service_interval_start'))
                                                ->formatStateUsing(function ($state) {
                                                    if ($state === null || $state === '' || $state === '-') {
                                                        return '—';
                                                    }
                                                    try {
                                                        return Carbon::parse($state)->format('d.m.Y');
                                                    } catch (\Throwable) {
                                                        return '—';
                                                    }
                                                }),

                                            TextEntry::make('end_date')
                                                ->label(__('intervention_plan.labels.monthly_plan_service_interval_end'))
                                                ->formatStateUsing(function ($state) {
                                                    if ($state === null || $state === '' || $state === '-') {
                                                        return '—';
                                                    }
                                                    try {
                                                        return Carbon::parse($state)->format('d.m.Y');
                                                    } catch (\Throwable) {
                                                        return '—';
                                                    }
                                                }),

                                            TextEntry::make('objective')
                                                ->label(__('intervention_plan.labels.service_objective')),

                                            RepeatableEntry::make('monthlyPlanInterventions')
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
                                                ->label(__('intervention_plan.labels.intervention_details'))
                                                ->columnSpanFull(),
                                        ]),
                                ]),
                        ]),
                ]),
        ]);
    }
}
