<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Forms\Components\Wizard;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

class CreateMonitoring extends CreateRecord
{
    use HasWizard;
    use HasParentResource;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        $breadcrumb = __('beneficiary.section.monitoring.breadcrumbs.file', ['file_number' => null]);

        return array_merge(
            BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForMonitoring(),
            [$breadcrumb],
        );
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.monitoring.titles.create');
    }

    protected function getRedirectUrl(): string
    {
        return static::getParentResource()::getUrl('monitorings.view', [
            'parent' => $this->parent,
            'record' => $this->record,
        ]);
    }

    protected function configureAction(Action $action): void
    {
        $action->hidden();
    }

    public function getSteps(): array
    {
        return [
            Wizard\Step::make(__('beneficiary.section.monitoring.headings.details'))
                ->schema(EditDetails::getSchema()),

            Wizard\Step::make(__('beneficiary.section.monitoring.headings.child_info'))
                ->schema(EditChildren::getSchema()),

            Wizard\Step::make(__('beneficiary.section.monitoring.headings.general'))
                ->schema(EditGeneral::getSchema()),

        ];
    }

    protected function getSubmitFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = ' . Js::from($this->previousUrl ?? static::getParentResource()::getUrl('monitorings.index', ['parent' => $this->parent])) . ')')
            ->color('gray');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the parent relationship key to the parent resource's ID.
        $data[$this->getParentRelationshipKey()] = $this->parent->id;

        return $data;
    }
}
