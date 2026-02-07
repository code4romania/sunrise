<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Services\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Enums\GeneralStatus;
use App\Filament\Organizations\Resources\Services\ServiceResource;
use App\Models\OrganizationServiceIntervention;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditService extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = ServiceResource::class;

    public function mount(int|string $record): void
    {
        try {
            parent::mount($record);
        } catch (\Throwable $e) {
            report($e);
            \Log::error('EditService mount failed', [
                'record' => $record,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    protected function getRedirectUrl(): ?string
    {
        return ServiceResource::getUrl('view', ['record' => $this->getRecord()]);
    }

    public function getTitle(): string|Htmlable
    {
        $name = $this->getRecord()->serviceWithoutStatusCondition?->name ?? '';

        return __('service.headings.edit_page', ['name' => strtolower($name)]);
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            BackAction::make()
                ->url($this->getRedirectUrl()),

            Action::make('deactivate')
                ->label(__('service.actions.change_status.inactivate'))
                ->color('danger')
                ->visible(fn (): bool => $record->status === GeneralStatus::ACTIVE)
                ->requiresConfirmation()
                ->modalHeading(__('service.headings.inactivate_modal'))
                ->modalDescription(__('service.helper_texts.inactivate_modal'))
                ->modalSubmitActionLabel(__('service.actions.change_status.inactivate_modal'))
                ->action(function (): void {
                    $this->getRecord()->update(['status' => GeneralStatus::INACTIVE]);
                    $this->getRecord()->refresh();
                }),

            Action::make('activate')
                ->label(__('service.actions.change_status.activate'))
                ->color('success')
                ->visible(fn (): bool => $record->status === GeneralStatus::INACTIVE)
                ->action(function (): void {
                    $this->getRecord()->update(['status' => GeneralStatus::ACTIVE]);
                    $this->getRecord()->refresh();
                }),

            DeleteAction::make()
                ->label(__('service.actions.delete'))
                ->outlined()
                ->disabled(fn () => $this->getRecord()->interventionServices()->exists()),
        ];
    }

    public function mutateFormDataBeforeFill(array $data): array
    {
        try {
            $record = $this->getRecord();
            $record->load('interventions.serviceInterventionWithoutStatusCondition');

            $data['service_id'] = $record->service_id;
            $data['interventions'] = $record->interventions->map(function (OrganizationServiceIntervention $i) {
                return [
                    'id' => $i->id,
                    'active' => true,
                    'name' => $i->serviceInterventionWithoutStatusCondition?->name,
                    'status' => $i->status,
                    'service_intervention_id' => $i->service_intervention_id,
                ];
            })->toArray();

            return $data;
        } catch (\Throwable $e) {
            report($e);

            throw $e;
        }
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $interventions = $this->data['interventions'] ?? [];
        $toKeep = [];

        foreach ($interventions as $item) {
            $active = $item['active'] ?? false;
            $serviceInterventionId = $item['service_intervention_id'] ?? null;
            if (! $serviceInterventionId || ! $active) {
                continue;
            }

            $toKeep[] = $serviceInterventionId;

            OrganizationServiceIntervention::query()->updateOrCreate(
                [
                    'organization_service_id' => $record->id,
                    'service_intervention_id' => $serviceInterventionId,
                ],
                [
                    'organization_id' => $record->organization_id,
                    'status' => (bool) ($item['status'] ?? true),
                ]
            );
        }

        $record->interventions()
            ->whereNotIn('service_intervention_id', $toKeep)
            ->delete();
    }
}
