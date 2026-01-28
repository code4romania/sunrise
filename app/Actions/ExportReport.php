<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ReportType;
use App\Exports\Report;
use App\Services\Reports\BeneficiariesV2;
use Filament\Infolists\Components\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportReport extends \Filament\Actions\Action
{
    protected ReportType | null $reportType = null;

    protected string | null $startDate = null;

    protected string | null $endDate = null;

    protected bool | null $showMissingValues = false;

    protected bool | null $addCasesInMonitoring = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->label(__('report.actions.export'));
        $this->icon('heroicon-o-arrow-down-tray');
        $this->outlined();
        $this->action(fn () => $this->generateExport());
    }

    public function setReportType(ReportType | string | null $reportType): self
    {
        $this->reportType = \is_string($reportType) ? ReportType::tryFrom($reportType) : $reportType;

        return $this;
    }

    public function setStartDate(?string $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function setEndDate(?string $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function setShowMissingValues(?bool $showMissingValues): self
    {
        $this->showMissingValues = $showMissingValues;

        return $this;
    }

    public function setAddCasesInMonitoring(?bool $addCasesInMonitoring): self
    {
        $this->addCasesInMonitoring = $addCasesInMonitoring;

        return $this;
    }

    public function generateExport(): BinaryFileResponse
    {
        $service = new BeneficiariesV2();
        $service->setReportType($this->reportType)
            ->setStartDate($this->startDate)
            ->setEndDate($this->endDate)
            ->setShowMissingValue($this->showMissingValues)
            ->setAddCasesInMonitoring($this->addCasesInMonitoring)
            ->composeReport();

        $fileName = \sprintf('%s_%s_%s.xlsx', $this->startDate, $this->endDate, $this->reportType->value);

        return Excel::download(new Report($service), $fileName);
    }
}
