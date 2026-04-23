<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\Concerns\ConfiguresBeneficiaryReportExport;
use App\Exports\Report;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportReport extends Action
{
    use ConfiguresBeneficiaryReportExport;

    protected function setUp(): void
    {
        parent::setUp();
        $this->label(__('report.actions.export_xls'));
        $this->icon('heroicon-o-arrow-down-tray');
        $this->outlined();
        $this->action(fn (): BinaryFileResponse => $this->generateExport());
    }

    public function generateExport(): BinaryFileResponse
    {
        $service = $this->makeComposedReportService();
        $reportName = $this->reportType !== null
            ? __('report.table_heading.'.$this->reportType->value)
            : '—';

        $fileName = \sprintf('%s_%s_%s.xls', $this->startDate, $this->endDate, $this->reportType->value);

        return Excel::download(new Report($service, [
            'report_name' => $reportName,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'includes_monitoring_cases' => (bool) $this->addCasesInMonitoring,
            'includes_missing_values' => (bool) $this->showMissingValues,
        ]), $fileName, \Maatwebsite\Excel\Excel::XLS);
    }
}
