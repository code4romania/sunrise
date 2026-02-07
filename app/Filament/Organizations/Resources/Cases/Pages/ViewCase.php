<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\Beneficiary;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
                    ->disabled(fn () => $record->status?->value === 'active'),
                Action::make('move_to_monitoring')
                    ->label(__('beneficiary.action.monitored'))
                    ->disabled(fn () => $record->status?->value === 'monitored'),
                Action::make('close_case')
                    ->label(__('beneficiary.action.closed'))
                    ->disabled(fn () => $record->status?->value === 'closed'),
                Action::make('archive_case')
                    ->label(__('beneficiary.action.archived'))
                    ->disabled(fn () => $record->status?->value === 'archived'),
                Action::make('reactivate_case')
                    ->label(__('beneficiary.action.reactivate')),
                Action::make('delete_case_file')
                    ->label(__('beneficiary.action.delete'))
                    ->color('danger'),
            ])
                ->label(__('case.view.case_actions'))
                ->button()
                ->color('primary'),

        ];
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
