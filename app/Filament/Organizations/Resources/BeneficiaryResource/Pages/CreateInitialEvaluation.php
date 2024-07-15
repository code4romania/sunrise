<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateInitialEvaluation extends EditRecord
{
    use HasWizard;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.initial_evaluation.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getBreadcrumbsForCreateInitialEvaluation();
    }

    public function getSteps(): array
    {
        return [
            Step::make(__('beneficiary.wizard.details.label'))
                ->schema(EditEvaluationDetails::getSchema()),
            Step::make(__('beneficiary.wizard.violence.label'))
                ->schema(EditViolence::getSchema()),
            Step::make(__('beneficiary.wizard.risk_factors.label'))
                ->schema(EditRiskFactors::getSchema()),
            Step::make(__('beneficiary.wizard.requested_services.label'))
                ->schema(EditRequestedServices::getSchema()),
            Step::make(__('beneficiary.wizard.beneficiary_situation.label'))
                ->schema(EditBeneficiarySituation::getSchema()),

        ];
    }
}
