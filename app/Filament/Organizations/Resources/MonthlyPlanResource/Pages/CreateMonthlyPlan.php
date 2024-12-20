<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonthlyPlanResource\Pages;

use App\Actions\BackAction;
use App\Concerns\HasParentResource;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\MonthlyPlanResource;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Facades\Filament;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Contracts\Support\Htmlable;

class CreateMonthlyPlan extends CreateRecord
{
    use HasWizard;
    use HasParentResource;
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = MonthlyPlanResource::class;

    protected array | null $services = null;

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->parent)
            ->getCreateMonthlyPlan();
    }

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.create_monthly_plan');
    }

    protected function getRedirectUrl(): string
    {
        return InterventionPlanResource::getUrl('view_monthly_plan', [
            'parent' => $this->parent,
            'record' => $this->getRecord(),
        ]);
    }

    public function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view_intervention_plan', [
                    'parent' => $this->parent->beneficiary,
                    'record' => $this->parent,
                ])),
        ];
    }

    protected function afterFill(): void
    {
        $copyLastPlan = (bool) request('copyLastPlan');

        if (! $copyLastPlan) {
            $this->services = [
                [
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'institution' => Filament::getTenant()->name,
                ],
            ];

            $this->form->fill([
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'case_manager_user_id' => $this->parent
                    ->beneficiary
                    ->managerTeam
                    ->first()
                    ?->user_id,
                'specialists' => $this->parent
                    ->beneficiary
                    ->specialistsTeam
                    ->pluck('id'),
                'intervention_plan_id' => $this->parent->id,
            ]);

            return;
        }

        $lastPlan = $this->parent
            ->monthlyPlans()
            ->with(['monthlyPlanServices.monthlyPlanInterventions'])
            ->orderByDesc('id')
            ->first();
        $this->services = $lastPlan?->monthlyPlanServices
            ->toArray();

        $this->form->fill($lastPlan
            ?->toArray());
    }

    public function getSteps(): array
    {
        return [
            Step::make('details')
                ->label(__('intervention_plan.headings.monthly_plan_details'))
                ->schema(EditMonthlyPlanDetails::getSchema($this->parent->beneficiary)),

            Step::make('services_and_interventions')
                ->label(__('intervention_plan.headings.services_and_interventions'))
                ->afterStateHydrated(function (Set $set, Get $get) {
                    if (! $this->services) {
                        return;
                    }

                    $set('monthlyPlanServices', $this->services);
                    foreach ($get('monthlyPlanServices') as $key => $service) {
                        $interventionPath = \sprintf('monthlyPlanServices.%s.monthlyPlanInterventions', $key);
                        $interventions = $this->services[$key]['monthly_plan_interventions'] ?? [[]];

                        $set($interventionPath, $interventions);
                    }
                })
                ->schema(EditMonthlyPlanServicesAndInterventions::getSchema()),
        ];
    }
}
