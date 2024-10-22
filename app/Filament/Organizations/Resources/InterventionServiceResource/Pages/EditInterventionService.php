<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\InterventionPlanResource\Widgets\ServicesWidget;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditInterventionService extends EditRecord
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
        return __('intervention_plan.headings.edit_page', [
            'service_name' => $this->getRecord()->organizationService->service->name,
        ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return InterventionPlanResource::getUrl('view_intervention_service', [
            'parent' => $this->getRecord()->interventionPlan,
            'record' => $this->getRecord(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(__('intervention_plan.actions.delete_service'))
                ->icon('heroicon-o-trash')
                ->modalHeading(__('intervention_plan.actions.delete_service'))
                ->outlined()
                ->successRedirectUrl(BeneficiaryResource::getUrl('view_intervention_plan', [
                    'parent' => $this->getRecord()->interventionPlan,
                    'record' => $this->getRecord(),
                ])),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->maxWidth('3xl')
                ->schema(ServicesWidget::getServiceSchema()),
        ]);
    }
}
