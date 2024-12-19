<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation;

use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\BeneficiaryPartner;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateDetailedEvaluation extends EditRecord
{
    use HasWizard;
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public function getBreadcrumb(): string
    {
        return __('beneficiary.breadcrumb.wizard_detailed_evaluation');
    }

    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl('view_detailed_evaluation', ['record' => $this->getRecord()]);
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('create_detailed_evaluation');
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

    public function afterSave(): void
    {
        $partnerRecord = $this->getRecord()->partner;
        if ($partnerRecord->same_as_legal_residence) {
            $partnerRecord->load(['legal_residence', 'effective_residence']);
            BeneficiaryPartner::copyLegalResidenceToEffectiveResidence($partnerRecord);
        }
    }
}
