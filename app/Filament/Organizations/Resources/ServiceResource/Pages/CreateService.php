<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\ServiceResource\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\ServiceResource;
use App\Models\OrganizationService;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;

class CreateService extends CreateRecord
{
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = ServiceResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string|Htmlable
    {
        return __('service.headings.create_page');
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl() => __('service.headings.navigation'),
            self::getResource()::getUrl('create') => $this->getTitle(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(ServiceResource::getUrl()),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->data['interventions'] = self::$resource::processInterventionsBeforeSave($this->data['interventions']);

        return parent::mutateFormDataBeforeCreate($data);
    }

    public function beforeCreate(): void
    {
        $serviceID = $this->data['service_id'];

        $organizationService = OrganizationService::query()
            ->where('service_id', $serviceID)
            ->first();

        if ($organizationService) {
            $this->redirect(ServiceResource::getUrl('view', ['record' => $organizationService]));
            throw new Halt();
        }
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->label(__('nomenclature.actions.create_service'));
    }
}
