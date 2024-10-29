<?php

declare(strict_types=1);

namespace App\Services\Breadcrumb;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Models\BeneficiaryIntervention;
use App\Models\InterventionPlan;
use App\Models\InterventionService;

class InterventionPlanBreadcrumb
{
    protected InterventionPlan $record;

    protected static string $resourcePath = InterventionPlanResource::class;

    protected static string $parentResource = BeneficiaryResource::class;

    public function __construct(InterventionPlan $record)
    {
        $this->record = $record;
    }

    public static function make(InterventionPlan $record)
    {
        return new static($record);
    }

    public function getInterventionPlanBreadcrumb(): array
    {
        return array_merge(
            BeneficiaryBreadcrumb::make($this->record->beneficiary)
                ->getBaseBreadcrumbs(),
            [self::$parentResource::getUrl('view_intervention_plan', [
                'parent' => $this->record->beneficiary,
                'record' => $this->record,
            ]) => __('intervention_plan.headings.view_page')]
        );
    }

    public function getServiceBreadcrumb(InterventionService $record): array
    {
        return array_merge(
            $this->getInterventionPlanBreadcrumb(),
            [self::$resourcePath::getUrl('view_intervention_service', [
                'parent' => $this->record,
                'record' => $record,
            ]) => $record->organizationServiceWithoutStatusCondition->serviceWithoutStatusCondition->name]
        );
    }

    public function getInterventionBreadcrumb(BeneficiaryIntervention $record): array
    {
        return array_merge(
            $this->getServiceBreadcrumb($record->interventionService),
            [InterventionServiceResource::getUrl('view_intervention', [
                'parent' => $record->interventionService,
                'record' => $record,
            ]) => $record->organizationServiceIntervention->serviceInterventionWithoutStatusCondition->name]
        );
    }
}
