<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Enums\ReportType;
use App\Services\Reports\BeneficiariesV2;
use Filament\Infolists\Components\Component;
use Illuminate\Support\Collection;

class ReportTable extends Component
{
    protected string $view = 'forms.components.report-table';

    protected BeneficiariesV2 $reportService;

    protected ReportType | null $reportType = null;

    protected string | null $startDate = null;

    protected string | null $endDate = null;

    protected bool | null $showMissingValues = false;

    protected bool | null $addCasesInMonitoring = false;

    public static function make(string | null $id = null): static
    {
        $static = app(static::class, ['id' => $id]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportService = new BeneficiariesV2();
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

    public function setAddCasesInMonitoring(?bool $addCasesInMonitoring): self
    {
        $this->addCasesInMonitoring = $addCasesInMonitoring;
        $this->reportService->setAddCasesInMonitoring($addCasesInMonitoring);

        return $this;
    }

    public function composeReport(): void
    {
        $this->reportService->composeReport();
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
}
