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

    public function view(): View
    {
        $verticalSubHeader = $this->reportService->getVerticalSubHeader();
        $horizontalSubHeader = $this->reportService->getHorizontalSubHeader();
        $firstHeaderElementColSpan = $verticalSubHeader ? 2 : 1;
        $firstHeaderElementRowSpan = $horizontalSubHeader ? 2 : 1;

        return view('exports.report-table', [
            'reportData' => $this->reportService->getReportData(),
            'header' => $this->reportService->getHorizontalHeader(),
            'subHeader' => $horizontalSubHeader,
            'subHeaderKey' => $this->reportService->getSubHeaderKey(),
            'verticalHeader' => $this->reportService->getVerticalHeader(),
            'verticalHeaderKey' => $this->reportService->getVerticalHeaderKey(),
            'verticalSubHeader' => $verticalSubHeader,
            'verticalSubHeaderKey' => $this->reportService->getVerticalSubHeaderKey(),
            'firstHeaderElementColSpan' => $firstHeaderElementColSpan,
            'firstHeaderElementRowSpan' => $firstHeaderElementRowSpan,
        ]);
    }
}
