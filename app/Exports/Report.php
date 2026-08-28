<?php

declare(strict_types=1);

namespace App\Exports;

use App\Services\Reports\BeneficiariesV2;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class Report implements FromView
{
    protected BeneficiariesV2 $reportService;

    /**
     * @var array<string, mixed>
     */
    protected array $exportContext;

    /**
     * @param  array<string, mixed>  $exportContext
     */
    public function __construct(BeneficiariesV2 $reportService, array $exportContext = [])
    {
        $this->reportService = $reportService;
        $this->exportContext = $exportContext;
    }

    /**
     * @return array<string, mixed>
     */
    public static function viewData(BeneficiariesV2 $reportService): array
    {
        $verticalSubHeader = $reportService->getVerticalSubHeader();
        $horizontalSubHeader = $reportService->getHorizontalSubHeader();
        $firstHeaderElementColSpan = $verticalSubHeader ? 2 : 1;
        $firstHeaderElementRowSpan = $horizontalSubHeader ? 2 : 1;

        return [
            'reportData' => $reportService->getReportData(),
            'header' => $reportService->getHorizontalHeader(),
            'subHeader' => $horizontalSubHeader,
            'subHeaderKey' => $reportService->getSubHeaderKey(),
            'verticalHeader' => $reportService->getVerticalHeader(),
            'verticalHeaderKey' => $reportService->getVerticalHeaderKey(),
            'verticalSubHeader' => $verticalSubHeader,
            'verticalSubHeaderKey' => $reportService->getVerticalSubHeaderKey(),
            'firstHeaderElementColSpan' => $firstHeaderElementColSpan,
            'firstHeaderElementRowSpan' => $firstHeaderElementRowSpan,
        ];
    }

    public function view(): View
    {
        $context = $this->exportContext;

        $startDate = isset($context['start_date']) && filled($context['start_date'])
            ? Carbon::parse((string) $context['start_date'])->format('d/m/Y')
            : '—';
        $endDate = isset($context['end_date']) && filled($context['end_date'])
            ? Carbon::parse((string) $context['end_date'])->format('d/m/Y')
            : '—';

        return view('exports.report-table', [
            ...static::viewData($this->reportService),
            'exportMeta' => [
                'report_name' => $context['report_name'] ?? '—',
                'calendar_interval' => $startDate.' - '.$endDate,
                'includes_monitoring_cases' => (bool) ($context['includes_monitoring_cases'] ?? false),
                'includes_missing_values' => (bool) ($context['includes_missing_values'] ?? false),
            ],
        ]);
    }
}
