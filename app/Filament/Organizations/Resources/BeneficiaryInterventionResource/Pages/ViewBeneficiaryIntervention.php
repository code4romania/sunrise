<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Widgets\BeneficiaryInterventionWidgets;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBeneficiaryIntervention extends ViewRecord
{
    use HasParentResource;

    protected static string $resource = BeneficiaryInterventionResource::class;

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->getRecord()->interventionService->interventionPlan)
            ->getInterventionBreadcrumb($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->organizationServiceIntervention->serviceIntervention->name;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
            ]);
    }

    protected function hasInfolist(): bool
    {
        return true;
    }

    protected function getFooterWidgets(): array
    {
        return [
            BeneficiaryInterventionWidgets::class,
        ];
    }
}
