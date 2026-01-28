<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\BeneficiaryResource\Widgets\ListSpecialistsWidget;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ListSpecialists extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_specialists');
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ListSpecialistsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.specialists.title');
    }
}
