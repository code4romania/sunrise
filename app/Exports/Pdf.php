<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Beneficiary;
use App\Services\Reports\BeneficiariesV2;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromView;

class Pdf implements FromView
{
    public static function getModel(): Model
    {
        return Beneficiary::make();
    }

    public static function getOptionsFormComponents()
    {
        return [];
    }

    public static function getColumns()
    {
        return [];
    }
    public function view(): View
    {
//        $verticalSubHeader = $this->reportService->getVerticalSubHeader();
//        $horizontalSubHeader = $this->reportService->getHorizontalSubHeader();
//        $firstHeaderElementColSpan = $verticalSubHeader ? 2 : 1;
//        $firstHeaderElementRowSpan = $horizontalSubHeader ? 2 : 1;

        return view('exports.export_pdf', [
//            'reportData' => $this->reportService->getReportData(),
//            'header' => $this->reportService->getHorizontalHeader(),
//            'subHeader' => $horizontalSubHeader,
//            'subHeaderKey' => $this->reportService->getSubHeaderKey(),
//            'verticalHeader' => $this->reportService->getVerticalHeader(),
//            'verticalHeaderKey' => $this->reportService->getVerticalHeaderKey(),
//            'verticalSubHeader' => $verticalSubHeader,
//            'verticalSubHeaderKey' => $this->reportService->getVerticalSubHeaderKey(),
//            'firstHeaderElementColSpan' => $firstHeaderElementColSpan,
//            'firstHeaderElementRowSpan' => $firstHeaderElementRowSpan,
        ]);
    }
}
