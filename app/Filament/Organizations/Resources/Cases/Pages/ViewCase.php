<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Actions\BackAction;
use App\Enums\CaseStatus;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\Beneficiary;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewCase extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        $record = $this->getRecord();

        return $record instanceof Beneficiary
            ? '#'.$record->id.' '.$record->full_name
            : parent::getTitle();
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            BackAction::make()
                ->label(__('case.view.breadcrumb_all'))
                ->url(CaseResource::getUrl('index')),
            Action::make('modification_history')
                ->label(__('case.view.modification_history'))
                ->link()
                ->url(fn () => CaseResource::getUrl('modification_history', ['record' => $record])),
            ActionGroup::make([
                Action::make('reopen_case')
                    ->label(__('beneficiary.action.active'))
                    ->disabled(fn () => $this->isStatusChangeDisabled($record, CaseStatus::ACTIVE))
                    ->action(fn () => $this->changeStatus($record, CaseStatus::ACTIVE)),
                Action::make('move_to_monitoring')
                    ->label(__('beneficiary.action.monitored'))
                    ->disabled(fn () => $this->isStatusChangeDisabled($record, CaseStatus::MONITORED))
                    ->action(fn () => $this->changeStatus($record, CaseStatus::MONITORED)),
                Action::make('close_case')
                    ->label(__('beneficiary.action.closed'))
                    ->disabled(fn () => $this->isStatusChangeDisabled($record, CaseStatus::CLOSED))
                    ->action(fn () => $this->changeStatus($record, CaseStatus::CLOSED)),
                Action::make('archive_case')
                    ->label(__('beneficiary.action.archived'))
                    ->disabled(fn () => $this->isStatusChangeDisabled($record, CaseStatus::ARCHIVED))
                    ->action(fn () => $this->changeStatus($record, CaseStatus::ARCHIVED)),
                Action::make('reactivate_case')
                    ->label(__('beneficiary.action.reactivate'))
                    ->disabled(fn () => $record->status !== CaseStatus::CLOSED && $record->status !== CaseStatus::ARCHIVED)
                    ->modalHeading(__('beneficiary.section.identity.headings.reactivate_modal'))
                    ->schema([
                        Placeholder::make('reactivate_text_1')
                            ->hiddenLabel()
                            ->content(__('beneficiary.placeholder.reactivate_text_1')),
                        Placeholder::make('reactivate_text_2')
                            ->hiddenLabel()
                            ->content(__('beneficiary.placeholder.reactivate_text_2')),
                        Placeholder::make('reactivate_text_3')
                            ->hiddenLabel()
                            ->content(__('beneficiary.placeholder.reactivate_text_3')),
                        Checkbox::make('confirm')
                            ->label(__('beneficiary.section.identity.labels.beneficiary_agreement'))
                            ->required(),
                    ])
                    ->modalSubmitActionLabel(__('beneficiary.action.reactivate_modal'))
                    ->action(fn () => $this->redirect(CaseResource::getUrl('create').'?parent='.$record->id)),
                Action::make('delete_case_file')
                    ->label(__('beneficiary.action.delete'))
                    ->color('danger')
                    ->requiresConfirmation()
                    ->authorize('delete', $record)
                    ->action(fn () => $record->delete())
                    ->successRedirectUrl(CaseResource::getUrl('index')),
            ])
                ->label(__('case.view.case_actions'))
                ->button()
                ->color('primary'),

        ];
    }

    protected function isStatusChangeDisabled(Beneficiary $record, CaseStatus $targetStatus): bool
    {
        if ($record->status === $targetStatus) {
            return true;
        }
        if (CaseStatus::isValue($record->status, CaseStatus::ACTIVE) && $targetStatus === CaseStatus::ARCHIVED) {
            return true;
        }
        if (CaseStatus::isValue($record->status, CaseStatus::MONITORED) && $targetStatus === CaseStatus::ARCHIVED) {
            return true;
        }
        if (CaseStatus::isValue($record->status, CaseStatus::CLOSED) && $targetStatus === CaseStatus::MONITORED) {
            return true;
        }
        if (CaseStatus::isValue($record->status, CaseStatus::ARCHIVED)
            && in_array($targetStatus, [CaseStatus::MONITORED, CaseStatus::ACTIVE], true)) {
            return true;
        }

        return false;
    }

    protected function changeStatus(Beneficiary $record, CaseStatus $status): void
    {
        $record->update(['status' => $status]);
        Notification::make()
            ->title(__('beneficiary.notification.change_status.title'))
            ->success()
            ->body(__('beneficiary.notification.change_status.body', [
                'status' => $status->getLabel(),
            ]))
            ->send();
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
        ];
    }
}
