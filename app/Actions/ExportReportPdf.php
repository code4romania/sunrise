<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\Concerns\ConfiguresBeneficiaryReportExport;
use App\Exports\Report;
use App\Services\CaseExports\Support\ExportBrandingResolver;
use App\Support\Utf8ForDompdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\Response;

class ExportReportPdf extends Action
{
    use ConfiguresBeneficiaryReportExport;

    protected function setUp(): void
    {
        parent::setUp();
        $this->label(__('report.actions.export_pdf'));
        $this->icon('heroicon-o-document-text');
        $this->outlined();
        $this->action(fn (): Response => $this->generateExport());
    }

    public function generateExport(): Response
    {
        $service = $this->makeComposedReportService();

        $title = __('report.table_heading.'.$this->reportType->value);
        $reportName = $this->reportType !== null
            ? __('report.table_heading.'.$this->reportType->value)
            : '—';
        $fileName = \sprintf('%s_%s_%s.pdf', $this->startDate, $this->endDate, $this->reportType->value);

        $viewData = Utf8ForDompdf::scrubReportStatisticsViewData(array_merge(
            [
                'title' => $title,
                'exportPeriodStart' => $this->startDate,
                'exportPeriodEnd' => $this->endDate,
                'branding' => app(ExportBrandingResolver::class)->resolve(),
                'exportMeta' => [
                    'report_name' => $reportName,
                    'calendar_interval' => sprintf(
                        '%s - %s',
                        $this->startDate ? \Illuminate\Support\Carbon::parse($this->startDate)->format('d/m/Y') : '—',
                        $this->endDate ? \Illuminate\Support\Carbon::parse($this->endDate)->format('d/m/Y') : '—'
                    ),
                    'includes_monitoring_cases' => (bool) $this->addCasesInMonitoring,
                    'includes_missing_values' => (bool) $this->showMissingValues,
                ],
            ],
            Report::viewData($service)
        ));

        $html = Utf8ForDompdf::scrubString(view('exports.reports.statistics-report-pdf', $viewData)->render());

        return Pdf::loadHTML($html)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isPhpEnabled', true)
            ->setOption('isFontSubsettingEnabled', false)
            ->setPaper('a4', 'landscape')
            ->download($fileName);
    }
}
