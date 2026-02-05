<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Services\Pages;

use App\Actions\BackAction;
use App\Enums\GeneralStatus;
use App\Filament\Organizations\Resources\Services\ServiceResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    public function getTitle(): string|Htmlable
    {
        $name = $this->getRecord()->serviceWithoutStatusCondition?->name ?? '';

        return __('service.headings.view_service_page', ['service_name' => strtolower($name)]);
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            BackAction::make()
                ->url(ServiceResource::getUrl()),

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

            EditAction::make()
                ->label(__('service.actions.change_service')),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->getRecord()->loadMissing([
            'serviceWithoutStatusCondition',
            'interventions.serviceInterventionWithoutStatusCondition',
            'interventions.beneficiaryInterventions.interventionPlan',
        ]);
    }
}
