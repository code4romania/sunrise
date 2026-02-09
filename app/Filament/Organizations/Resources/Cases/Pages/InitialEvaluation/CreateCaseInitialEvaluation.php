<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InitialEvaluation;

use App\Actions\BackAction;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Schemas\BeneficiaryResource\InitialEvaluationSchema;
use App\Models\Beneficiary;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Support\Htmlable;

class CreateCaseInitialEvaluation extends EditRecord
{
    use HasWizard;
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.initial_evaluation.title');
    }

    protected function getRedirectUrl(): string
    {
        return CaseResource::getUrl('view', ['record' => $this->getRecord()]);
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        $breadcrumbs = [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
        ];
        if ($record instanceof Beneficiary) {
            $breadcrumbs[CaseResource::getUrl('view', ['record' => $record])] = $record->getBreadcrumb();
        }
        $breadcrumbs[''] = request()->routeIs('*.edit_initial_evaluation')
            ? __('beneficiary.page.initial_evaluation.title')
            : __('beneficiary.page.create_initial_evaluation.title');

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $record])),
        ];
    }

    public function getSteps(): array
    {
        return [
            Step::make(__('beneficiary.wizard.details.label'))
                ->schema(InitialEvaluationSchema::getEvaluationDetailsFormComponents()),
            Step::make(__('beneficiary.wizard.violence.label'))
                ->schema(InitialEvaluationSchema::getViolenceFormComponents()),
            Step::make(__('beneficiary.wizard.risk_factors.label'))
                ->schema(InitialEvaluationSchema::getRiskFactorsFormComponents()),
            Step::make(__('beneficiary.wizard.requested_services.label'))
                ->schema(InitialEvaluationSchema::getRequestedServicesFormComponents()),
            Step::make(__('beneficiary.wizard.beneficiary_situation.label'))
                ->schema(InitialEvaluationSchema::getBeneficiarySituationFormComponents()),
        ];
    }
}
