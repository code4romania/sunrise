<?php

declare(strict_types=1);

namespace App\Exports;

use App\Services\Reports\BeneficiariesV2;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class Report implements FromView
{
    protected BeneficiariesV2 $reportService;

    public function __construct(BeneficiariesV2 $reportService)
    {
        $this->reportService = $reportService;
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
        return view('exports.report-table', static::viewData($this->reportService));
    }
}
