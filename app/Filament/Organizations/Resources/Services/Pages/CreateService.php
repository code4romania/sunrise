<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Services\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\Services\Schemas\ServiceForm;
use App\Filament\Organizations\Resources\Services\ServiceResource;
use App\Models\OrganizationService;
use App\Models\OrganizationServiceIntervention;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateService extends CreateRecord
{
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = ServiceResource::class;

    protected static bool $canCreateAnother = false;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $pendingInterventions = [];

    public function getTitle(): string|Htmlable
    {
        return __('service.headings.create_page');
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(ServiceResource::getUrl()),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label(__('nomenclature.actions.create_service'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingInterventions = ServiceForm::processInterventionsBeforeSave($data['interventions'] ?? []) ?? [];

        return [
            'service_id' => $data['service_id'],
        ];
    }

    public function beforeCreate(): void
    {
        $tenant = Filament::getTenant();
        $serviceId = $this->data['service_id'] ?? null;

        if (! $tenant || ! $serviceId) {
            return;
        }

        $existing = OrganizationService::query()
            ->where('organization_id', $tenant->id)
            ->where('service_id', $serviceId)
            ->first();

        if ($existing) {
            $this->redirect(ServiceResource::getUrl('view', ['record' => $existing]));

            throw new Halt;
        }
    }

    protected function afterCreate(): void
    {
        $interventions = $this->pendingInterventions;

        /** @var OrganizationService $organizationService */
        $organizationService = $this->record;

        foreach ($interventions as $item) {
            $serviceInterventionId = $item['service_intervention_id'] ?? null;
            if (! $serviceInterventionId) {
                continue;
            }

            OrganizationServiceIntervention::query()->firstOrCreate(
                [
                    'organization_service_id' => $organizationService->id,
                    'service_intervention_id' => $serviceInterventionId,
                ],
                [
                    'organization_id' => $organizationService->organization_id,
                    'status' => true,
                ]
            );
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        return parent::handleRecordCreation([
            'service_id' => $data['service_id'],
        ]);
    }
}
