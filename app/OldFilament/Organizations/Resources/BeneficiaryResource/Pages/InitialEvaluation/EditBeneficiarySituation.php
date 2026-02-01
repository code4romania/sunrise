<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToInitialEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Schemas\BeneficiaryResource\InitialEvaluationSchema;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditBeneficiarySituation extends EditRecord
{
    use PreventSubmitFormOnEnter;
    use RedirectToInitialEvaluation;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_beneficiary_situation.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_initial_evaluation');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.beneficiary_situation.label'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(InitialEvaluationSchema::getBeneficiarySituationFormComponents());
    }

    public static function getInfoListSchema(): array
    {
        return InitialEvaluationSchema::getBeneficiarySituationInfolistComponents();
    }
}
