<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Concerns\InteractsWithBeneficiaryDetailsPanel;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\MonthlyPlanServicesAndInterventionsFormSchema;
use App\Models\Beneficiary;
use App\Models\MonthlyPlan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditCaseMonthlyPlanServicesAndInterventions extends EditRecord
{
    use InteractsWithBeneficiaryDetailsPanel;
    use PreventSubmitFormOnEnter;

    protected static string $resource = CaseResource::class;

    protected ?Beneficiary $beneficiary = null;

    public function mount(int|string $record): void
    {
        $this->beneficiary = CaseResource::resolveRecordRouteBinding($record);
        if (! $this->beneficiary instanceof Beneficiary) {
            abort(404);
        }

        $plan = $this->beneficiary->interventionPlan;
        if (! $plan) {
            $this->redirect(CaseResource::getUrl('view_intervention_plan', ['record' => $this->beneficiary]));

            return;
        }

        $monthlyPlanId = request()->route('monthlyPlan');
        $monthlyPlanModel = MonthlyPlan::query()
            ->where('intervention_plan_id', $plan->id)
            ->where('id', $monthlyPlanId)
            ->firstOrFail();

        $this->record = $monthlyPlanModel;
        $this->authorizeAccess();
        $this->fillForm();
        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(CaseResource::canEdit($this->resolveCaseBeneficiary()), 403);
    }

    protected function resolveCaseBeneficiary(): Beneficiary
    {
        if ($this->beneficiary instanceof Beneficiary) {
            return $this->beneficiary;
        }

        $monthlyPlan = $this->getRecord();
        if (! $monthlyPlan instanceof MonthlyPlan) {
            abort(404);
        }

        $monthlyPlan->loadMissing(['beneficiary', 'interventionPlan.beneficiary']);

        $beneficiary = $monthlyPlan->beneficiary ?? $monthlyPlan->interventionPlan?->beneficiary;
        if (! $beneficiary instanceof Beneficiary) {
            abort(404);
        }

        return $beneficiary;
    }

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.edit_monthly_plan_services_and_interventions_title');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->resolveCaseBeneficiary();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record->getBreadcrumb(),
            CaseResource::getUrl('view_intervention_plan', ['record' => $record]) => __('intervention_plan.headings.view_page'),
            CaseResource::getUrl('view_monthly_plan', ['record' => $record, 'monthlyPlan' => $this->getRecord()]) => __('intervention_plan.headings.monthly_plan'),
            '' => __('intervention_plan.headings.edit_monthly_plan_services_and_interventions_title'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view_monthly_plan', [
                    'record' => $this->resolveCaseBeneficiary(),
                    'monthlyPlan' => $this->getRecord(),
                    'tab' => '-'.Str::slug(__('intervention_plan.headings.services_and_interventions')).'-tab',
                ])),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return CaseResource::getUrl('view_monthly_plan', [
            'record' => $this->resolveCaseBeneficiary(),
            'monthlyPlan' => $this->getRecord(),
            'tab' => '-'.Str::slug(__('intervention_plan.headings.services_and_interventions')).'-tab',
        ]);
    }

    /**
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    public function getModel(): string
    {
        return MonthlyPlan::class;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(
            $this->getFormSchema(),
        );
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getFormSchema(): array
    {
        return [
            MonthlyPlanServicesAndInterventionsFormSchema::monthlyPlanServicesRepeater(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('filament-actions::edit.single.notifications.saved.title'));
    }
}
