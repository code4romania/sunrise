<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Actions\ExportReport;
use App\Actions\ExportReport2;
use App\Enums\ReportType;
use App\Exports\Report;
use App\Services\Reports\Beneficiaries;
use Filament\Infolists\Components\Component;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ReportTable extends Component
{
    protected string $view = 'forms.components.report-table';

    protected Beneficiaries $reportService;

    protected ReportType | null $reportType = null;

    protected string | null $startDate = null;

    protected string | null $endDate = null;

    protected bool | null $showMissingValues = false;

    public static function make(string | null $id = null): static
    {
        $static = app(static::class, ['id' => $id]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportService = new Beneficiaries();
    }

    public function setReportType(ReportType | string | null $reportType): self
    {
        $this->reportType = $reportType;
        $this->reportService->setReportType($reportType);

        return $this;
    }

    public function setStartDate(?string $startDate): self
    {
        $this->startDate = $startDate;
        $this->reportService->setStartDate($startDate);

        return $this;
    }

    public function setEndDate(?string $endDate): self
    {
        $this->endDate = $endDate;
        $this->reportService->setEndDate($endDate);

        return $this;
    }

    public function setShowMissingValue(?bool $showMissingValue): self
    {
        $this->showMissingValues = $showMissingValue;
        $this->reportService->setShowMissingValue($showMissingValue);

        return $this;
    }

    public function composeReport(): void
    {
        $this->reportService->composeReport();
    }

    public function getReportType(): ?ReportType
    {
        return $this->reportService->getReportType();
    }

    public function getReportData(): Collection
    {
        return $this->reportService->getReportData() ?? collect();
    }

    public function getHorizontalHeader(): array
    {
        return $this->reportService->getHorizontalHeader();
    }

    public function getHorizontalSubHeader(): array
    {
        return $this->reportService->getHorizontalSubHeader();
    }

    public function getSubHeaderKey(): ?string
    {
        return $this->reportService->getSubHeaderKey();
    }

    public function getVerticalHeader(): array
    {
        return $this->reportService->getVerticalHeader();
    }

    public function getVerticalHeaderKey(): ?string
    {
        return $this->reportService->getVerticalHeaderKey();
    }

    public function getVerticalSubHeader(): array
    {
        return $this->reportService->getVerticalSubHeader();
    }

    public function getVerticalSubHeaderKey(): ?string
    {
        return $this->reportService->getVerticalSubHeaderKey();
    }

    public function exportToExcel()
    {
        return Excel::download(new Report($this->reportService), 'my-custom-export.xlsx');
    }

    public function getExportAction()
    {
        return ExportReport2::make('aaaa');

        return ExportReport::make('export_report')
            ->setReportType($this->reportType)
            ->setStartDate($this->startDate)
            ->setEndDate($this->endDate)
            ->setShowMissingValues($this->showMissingValues);
    }
}
