<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Actions\BackAction;
use App\Exports\CaseModificationHistoryExport;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Pages\Widgets\ModificationHistoryWidget;
use App\Models\Activity;
use App\Models\Beneficiary;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ViewCaseModificationHistory extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('case.view.modification_history');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            '' => __('case.view.modification_history'),
        ];
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $record])),
            Action::make('download_excel')
                ->label(__('case.view.modification_history_download'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn (): BinaryFileResponse => $this->downloadExport('xlsx')),
            Action::make('download_csv')
                ->label(__('case.view.modification_history_download_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn (): BinaryFileResponse => $this->downloadExport('csv')),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->schema(fn (): array => $this->getWidgetsSchemaComponents([ModificationHistoryWidget::class])),
            ]);
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    protected function hasInfolist(): bool
    {
        return false;
    }

    private function getActivitiesForExport(): Collection
    {
        $record = $this->getRecord();
        if (! $record instanceof Beneficiary) {
            return collect();
        }

        return Activity::query()
            ->whereMorphedTo('subject', $record)
            ->with('causer')
            ->orderByDesc('created_at')
            ->get();
    }

    private function downloadExport(string $format): BinaryFileResponse
    {
        $record = $this->getRecord();
        $activities = $this->getActivitiesForExport();
        $fileName = 'istoric-modificari-caz-'.($record?->id ?? '0').'-'.now()->format('Y-m-d');

        return Excel::download(
            new CaseModificationHistoryExport($activities),
            $fileName.'.'.$format,
            $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX
        );
    }
}
