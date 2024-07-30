<?php

declare(strict_types=1);

namespace App\Services\Breadcrumb;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Beneficiary as BeneficiaryModel;

class Beneficiary
{
    protected BeneficiaryModel $record;

    protected static string $resourcePath = BeneficiaryResource::class;

    public function __construct(BeneficiaryModel $record)
    {
        $this->record = $record;
    }

    public static function make(BeneficiaryModel $record)
    {
        return new static($record);
    }

    public function getBaseBreadcrumbs(): array
    {
        return array_merge(
            [self::$resourcePath::getUrl() => self::$resourcePath::getBreadcrumb()],
            [self::$resourcePath::getUrl('view', ['record' => $this->record->id]) => $this->record->getBreadcrumb()],
        );
    }

    public function getIdentityBreadcrumbs(): array
    {
        $breadcrumb = __('beneficiary.section.identity.title');

        return array_merge(
            $this->getBaseBreadcrumbs(),
            [self::$resourcePath::getUrl('view_identity', ['record' => $this->record->id]) => $breadcrumb],
        );
    }

    public function getPersonalInformationBreadcrumbs(): array
    {
        $breadcrumb = __('beneficiary.breadcrumb.personal_information');

        return array_merge(
            $this->getBaseBreadcrumbs(),
            [self::$resourcePath::getUrl('view_personal_information', ['record' => $this->record->id]) => $breadcrumb],
        );
    }

    public function getBreadcrumbsForInitialEvaluation(): array
    {
        $breadcrumb = __('beneficiary.page.create_initial_evaluation.title');

        return array_merge(
            $this->getBaseBreadcrumbs(),
            [self::$resourcePath::getUrl('view_initial_evaluation', ['record' => $this->record->id]) => $breadcrumb],
        );
    }

    public function getBreadcrumbsForCreateInitialEvaluation(): array
    {
        $breadcrumb = __('beneficiary.page.create_initial_evaluation.title');

        return array_merge(
            $this->getBaseBreadcrumbs(),
            [self::$resourcePath::getUrl('create_initial_evaluation', ['record' => $this->record->id]) => $breadcrumb],
        );
    }

    public function getBreadcrumbsForDetailedEvaluation(): array
    {
        $breadcrumb = __('beneficiary.breadcrumb.wizard_detailed_evaluation');

        return array_merge(
            $this->getBaseBreadcrumbs(),
            [self::$resourcePath::getUrl('view_detailed_evaluation', ['record' => $this->record->id]) => $breadcrumb],
        );
    }

    public function getBreadcrumbsForSpecialists(): array
    {
        $breadcrumb = __('beneficiary.section.specialists.title');

        return array_merge(
            $this->getBaseBreadcrumbs(),
            [self::$resourcePath::getUrl('view_specialists', ['record' => $this->record->id]) => $breadcrumb],
        );
    }

    public function getBreadcrumbsForDocuments(): array
    {
        $breadcrumb = __('beneficiary.section.documents.title.page');

        return array_merge(
            $this->getBaseBreadcrumbs(),
            [self::$resourcePath::getUrl('documents.index', ['parent' => $this->record->id]) => $breadcrumb],
        );
    }
}
