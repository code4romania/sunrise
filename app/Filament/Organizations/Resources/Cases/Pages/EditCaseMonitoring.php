<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Concerns\InteractsWithBeneficiaryDetailsPanel;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Pages\Widgets\MonitoringWidget;
use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\MonitoringResource;
use App\Models\Beneficiary;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditCaseMonitoring extends ViewRecord
{
    use InteractsWithBeneficiaryDetailsPanel;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.list');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            '' => __('monitoring.titles.list'),
        ];
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $record])),
            Action::make('create_monitoring_modal')
                ->label(__('monitoring.actions.create'))
                ->modalHeading(__('monitoring.headings.modal_create'))
                ->modalDescription(__('monitoring.labels.modal_create_description'))
                ->modalSubmitAction(
                    Action::make('create_from_last')
                        ->label(__('monitoring.actions.create_from_last'))
                        ->url(fn (): string => MonitoringResource::getUrl('create', ['beneficiary' => $record]).'?copyLastFile=1')
                )
                ->modalCancelAction(
                    Action::make('create_simple')
                        ->label(__('monitoring.actions.create_simple'))
                        ->outlined()
                        ->url(fn (): string => MonitoringResource::getUrl('create', ['beneficiary' => $record]))
                )
                ->visible(fn (): bool => $record instanceof Beneficiary && $record->monitoring()->count() > 0),
            Action::make('create_monitoring_direct')
                ->label(__('monitoring.actions.create'))
                ->url(fn (): string => MonitoringResource::getUrl('create', ['beneficiary' => $record]))
                ->visible(fn (): bool => $record instanceof Beneficiary && $record->monitoring()->count() === 0),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->schema(fn (): array => $this->getWidgetsSchemaComponents([MonitoringWidget::class])),
            ]);
    }

    /**
     * Return empty so the table is only shown once in content(); otherwise it would also appear in the page footer.
     *
     * @return array<int, class-string<\Filament\Widgets\Widget>>
     */
    protected function getFooterWidgets(): array
    {
        return [];
    }

    protected function hasInfolist(): bool
    {
        return false;
    }
}
