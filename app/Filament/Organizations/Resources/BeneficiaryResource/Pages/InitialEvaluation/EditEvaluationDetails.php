<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation;

use App\Filament\Organizations\Schemas\BeneficiaryResource\InitialEvaluationSchema;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToInitialEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditEvaluationDetails extends EditRecord
{
    use RedirectToInitialEvaluation;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_evaluation_details.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(InitialEvaluationSchema::getEvaluationDetailsFormComponents());
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_initial_evaluation');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.details.label'));
    }

    public static function getInfoListSchema(): array
    {
        return InitialEvaluationSchema::getEvaluationDetailsInfolistComponents();
    }
}
