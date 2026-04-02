<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\MonthlyPlanServicesAndInterventionsFormSchema;
use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\InterventionPlan;
use App\Models\MonthlyPlan;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CreateCaseMonthlyPlan extends CreateRecord
{
    use HasWizard {
        HasWizard::getWizardComponent as parentGetWizardComponent;
    }

    protected static string $resource = CaseResource::class;

    protected static bool $canCreateAnother = false;

    protected ?Beneficiary $beneficiary = null;

    /**
     * Route `{case}` (id/slug/model) or value passed when testing Livewire.
     */
    public Beneficiary|int|string|null $case = null;

    public function mount(): void
    {
        $rawCase = $this->case ?? request()->route('case');
        $this->record = match (true) {
            $rawCase instanceof Beneficiary => $rawCase,
            $rawCase !== null => CaseResource::resolveRecordRouteBinding($rawCase),
            default => null,
        };
        if ($this->record === null || ! $this->record instanceof Beneficiary) {
            abort(404);
        }

        $this->beneficiary = $this->record;

        $plan = $this->beneficiary->interventionPlan;
        if (! $plan) {
            $this->redirect(CaseResource::getUrl('view_intervention_plan', ['record' => $this->beneficiary]));

            return;
        }

        $copyLastPlan = (bool) request('copyLastPlan');
        if ($copyLastPlan) {
            $newPlan = $this->duplicateLastMonthlyPlan($plan);
            if ($newPlan) {
                $this->redirect(CaseResource::getUrl('view_monthly_plan', [
                    'record' => $this->beneficiary,
                    'monthlyPlan' => $newPlan,
                ]));

                return;
            }
        }

        $this->authorizeAccess();
        $this->fillForm();
        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        $beneficiary = $this->getBeneficiary();
        abort_unless($beneficiary !== null && CaseResource::canEdit($beneficiary), 403);
    }

    protected function getBeneficiary(): ?Beneficiary
    {
        if ($this->beneficiary !== null) {
            return $this->beneficiary;
        }
        if ($this->record instanceof Beneficiary) {
            return $this->record;
        }
        $record = $this->getRecord();
        if ($record instanceof MonthlyPlan && $record->relationLoaded('interventionPlan')) {
            return $record->interventionPlan?->beneficiary;
        }

        return null;
    }

    /**
     * Seeds one empty `monthlyPlanServices` row: Filament skips `loadStateFromRelationships` for
     * relationship repeaters until the parent `MonthlyPlan` is persisted, so defaults would not apply.
     */
    protected function fillForm(): void
    {
        $beneficiary = $this->getBeneficiary();
        $this->callHook('beforeFill');
        $this->form->fill([
            'start_date' => Carbon::today()->format('Y-m-d'),
            'end_date' => Carbon::today()->addMonth()->format('Y-m-d'),
            'case_manager_user_id' => $beneficiary?->managerTeam?->first()?->user_id ?? auth()->id(),
            'specialists' => $beneficiary?->specialistsTeam()->pluck('id')->values()->all() ?? [],
            'monthlyPlanServices' => [
                (string) Str::uuid() => [
                    'monthlyPlanInterventions' => [],
                ],
            ],
        ]);
        $this->callHook('afterFill');
    }

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.create_monthly_plan');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getBeneficiary() ?? $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            CaseResource::getUrl('view_intervention_plan', ['record' => $record]) => __('intervention_plan.headings.view_page'),
            '' => __('intervention_plan.headings.create_monthly_plan'),
        ];
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getBeneficiary() ?? $this->getRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view_intervention_plan', ['record' => $record])),
        ];
    }

    /**
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    public function getModel(): string
    {
        return MonthlyPlan::class;
    }

    public function getSteps(): array
    {
        return [
            Step::make(__('intervention_plan.wizard.monthly_plan_general'))
                ->id('general')
                ->schema([
                    Grid::make(2)
                        ->maxWidth('3xl')
                        ->schema([
                            DatePicker::make('start_date')
                                ->label(__('intervention_plan.labels.monthly_plan_start_date'))
                                ->placeholder('ZZ/LL/AN')
                                ->required(),
                            DatePicker::make('end_date')
                                ->label(__('intervention_plan.labels.monthly_plan_end_date'))
                                ->placeholder('ZZ/LL/AN')
                                ->required(),
                            Select::make('case_manager_user_id')
                                ->label(__('intervention_plan.labels.case_manager'))
                                ->options(User::getTenantOrganizationUsers()->all())
                                ->required()
                                ->placeholder(__('intervention_plan.placeholders.specialist')),
                            Select::make('specialists')
                                ->label(__('intervention_plan.labels.case_team'))
                                ->multiple()
                                ->options(fn (): Collection => $this->getBeneficiary()?->specialistsTeam()->with('user', 'roleForDisplay')->get()->pluck('name_role', 'id') ?? collect())
                                ->placeholder(__('intervention_plan.placeholders.specialists')),
                        ]),
                ]),
            Step::make(__('intervention_plan.headings.services_and_interventions'))
                ->id('services')
                ->schema([
                    MonthlyPlanServicesAndInterventionsFormSchema::monthlyPlanServicesRepeater(),
                ]),
        ];
    }

    public function getWizardComponent(): Component
    {
        return $this->parentGetWizardComponent()
            ->persistStepInQueryString('monthly-plan-step');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $beneficiary = $this->getBeneficiary();
        abort_unless($beneficiary?->interventionPlan !== null, 404);
        $data['intervention_plan_id'] = $beneficiary->interventionPlan->id;
        $data['specialists'] = $data['specialists'] ?? [];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        $record = $this->getRecord();
        $beneficiary = $this->getBeneficiary()
            ?? $record->beneficiary
            ?? $record->interventionPlan?->beneficiary;

        return CaseResource::getUrl('view_monthly_plan', [
            'record' => $beneficiary,
            'monthlyPlan' => $record,
        ]);
    }

    private function duplicateLastMonthlyPlan(InterventionPlan $plan): ?MonthlyPlan
    {
        $last = $plan->monthlyPlans()
            ->with(['monthlyPlanServices.monthlyPlanInterventions'])
            ->orderByDesc('id')
            ->first();

        if (! $last) {
            return null;
        }

        $newMonthly = $plan->monthlyPlans()->create([
            'start_date' => $last->start_date,
            'end_date' => $last->end_date,
            'case_manager_user_id' => $last->case_manager_user_id,
            'specialists' => $last->specialists,
        ]);

        foreach ($last->monthlyPlanServices as $oldService) {
            $newService = $newMonthly->monthlyPlanServices()->create([
                'service_id' => $oldService->service_id,
                'institution' => $oldService->institution,
                'responsible_person' => $oldService->responsible_person,
                'start_date' => $oldService->start_date,
                'end_date' => $oldService->end_date,
                'objective' => $oldService->objective,
                'service_details' => $oldService->service_details,
            ]);

            foreach ($oldService->monthlyPlanInterventions as $oldIntervention) {
                $newService->monthlyPlanInterventions()->create([
                    'service_intervention_id' => $oldIntervention->service_intervention_id,
                    'objections' => $oldIntervention->objections,
                    'observations' => $oldIntervention->observations,
                    'expected_results' => $oldIntervention->expected_results,
                    'procedure' => $oldIntervention->procedure,
                    'indicators' => $oldIntervention->indicators,
                    'achievement_degree' => $oldIntervention->achievement_degree,
                ]);
            }
        }

        return $newMonthly;
    }
}
