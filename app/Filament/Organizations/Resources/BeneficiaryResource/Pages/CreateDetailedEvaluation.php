<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateDetailedEvaluation extends EditRecord
{
    use HasWizard;

    protected static string $resource = BeneficiaryResource::class;

    public function getBreadcrumb(): string
    {
        return __('beneficiary.breadcrumb.wizard_detailed_evaluation');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getBreadcrumbsForDetailedEvaluation();
    }

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.create_detailed_evaluation.title');
    }

    public function getSteps(): array
    {
        return [
            Step::make(__('beneficiary.wizard.detailed_evaluation.label'))
                ->schema(EditDetailedEvaluation::getSchema()),

            Step::make(__('beneficiary.wizard.partner.label'))
                ->schema(EditBeneficiaryPartner::getSchema()),

            Step::make(__('beneficiary.wizard.multidisciplinary_evaluation.label'))
                ->schema(EditMultidisciplinaryEvaluation::getSchema()),

            Step::make(__('beneficiary.wizard.results.label'))
                ->schema(EditDetailedEvaluationResult::getSchema()),
        ];
    }
}
