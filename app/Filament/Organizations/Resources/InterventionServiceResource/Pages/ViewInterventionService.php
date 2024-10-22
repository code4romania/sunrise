<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewInterventionService extends ViewRecord
{
    use HasParentResource;

    protected static string $resource = InterventionServiceResource::class;

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->getRecord()->interventionPlan)
            ->getServiceBreadcrumb($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->organizationService->service->name;
    }

    protected function getFooterWidgets(): array
    {
        return [
            InterventionServiceResource\Widgets\ServiceWidget::class,
        ];
    }
}
