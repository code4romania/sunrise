<?php

declare(strict_types=1);

namespace App\Services\Breadcrumb;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use App\Models\Monitoring;

class BeneficiaryBreadcrumb
{
    protected Beneficiary $record;

    protected static string $resourcePath = BeneficiaryResource::class;

    public function __construct(Beneficiary $record)
    {
        $this->record = $record;
    }

    public static function make(Beneficiary $record)
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

    public function getBreadcrumbs(string $page): array
    {
        $breadcrumb = match ($page) {
            'view_identity' => __('beneficiary.page.identity.title'),
            'view_personal_information' => __('beneficiary.breadcrumb.personal_information'),
            'view_initial_evaluation', 'create_initial_evaluation' => __('beneficiary.page.create_initial_evaluation.title'),
            'view_detailed_evaluation', 'create_detailed_evaluation' => __('beneficiary.breadcrumb.wizard_detailed_evaluation'),
            'view_specialists' => __('beneficiary.section.specialists.title'),
            'documents.index' => __('beneficiary.section.documents.title.page'),
            'monitorings.index' => __('monitoring.breadcrumbs.general'),
            'beneficiary-histories.index' => __('beneficiary.section.history.breadcrumbs.list'),
            'view_close_file' => __('beneficiary.section.close_file.titles.create'),
            'create_close_file' => __('beneficiary.section.close_file.titles.create')
        };

        $params = match ($page) {
            'documents.index', 'monitorings.index', 'beneficiary-histories.index' => ['parent' => $this->record->id],

            default => ['record' => $this->record->id],
        };

        return array_merge(
            $this->getBaseBreadcrumbs(),
            [self::$resourcePath::getUrl($page, $params) => $breadcrumb],
        );
    }

    public function getBreadcrumbsForCreateMonitoring(): array
    {
        $breadcrumb = __('monitoring.titles.create');
        return array_merge(
            $this->getBreadcrumbs('monitorings.index'),
            [self::$resourcePath::getUrl('monitorings.create', ['parent' => $this->record]) => $breadcrumb]
        );
    }

    public function getBreadcrumbsForMonitoringFile(Monitoring $monitoringRecord): array
    {
        $url = self::$resourcePath::getUrl('monitorings.view', ['parent' => $this->record, 'record' => $monitoringRecord]);
        $breadcrumb = __('monitoring.breadcrumbs.file', ['file_number' => $monitoringRecord->number]);

        return array_merge(
            $this->getBreadcrumbs('monitorings.index'),
            [$url => $breadcrumb]
        );
    }

    public function getBreadcrumbsForMonitoringFileEdit(Monitoring $monitoringRecord): array
    {
        $breadcrumb = __('general.action.edit');

        return array_merge(
            $this->getBreadcrumbsForMonitoringFile($monitoringRecord),
            [$breadcrumb]
        );
    }

}
