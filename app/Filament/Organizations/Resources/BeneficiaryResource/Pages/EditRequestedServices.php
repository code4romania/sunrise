<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToInitialEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditRequestedServices extends EditRecord
{
    use RedirectToInitialEvaluation;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_requested_services.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbsForInitialEvaluation();
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.requested_services.label'));
    }

    public function form(Form $form): Form
    {
        return $form->schema(
            self::getSchema()
        );
    }

    public static function getSchema(): array
    {
        return [
            Group::make()
                ->relationship('requestedServices')
                ->schema([
                    Section::make(__('beneficiary.section.initial_evaluation.heading.types_of_requested_services'))
                        ->schema(EditDetailedEvaluationResult::getRecommendationServicesSchema()),
                ]),
        ];
    }

    public static function getInfoListSchema(): array
    {
        return [
            InfolistGroup::make()
                ->relationship('requestedServices')
                ->schema([
                    InfolistSection::make(__('beneficiary.section.initial_evaluation.heading.types_of_requested_services'))
                        ->schema(EditDetailedEvaluationResult::getRecommendationServicesInfolistSchema()),
                ]),
        ];
    }
}
