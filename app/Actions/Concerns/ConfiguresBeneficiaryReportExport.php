<?php

declare(strict_types=1);

namespace App\Actions\Concerns;

use App\Enums\ReportType;
use App\Services\Reports\BeneficiariesV2;

trait ConfiguresBeneficiaryReportExport
{
    protected ?ReportType $reportType = null;

    protected ?string $startDate = null;

    protected ?string $endDate = null;

    protected ?bool $showMissingValues = false;

    protected ?bool $addCasesInMonitoring = false;

    public function setReportType(ReportType|string|null $reportType): static
    {
        $this->reportType = \is_string($reportType) ? ReportType::tryFrom($reportType) : $reportType;

        return $this;
    }

    public function setStartDate(?string $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function setEndDate(?string $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function setShowMissingValues(?bool $showMissingValues): static
    {
        $this->showMissingValues = $showMissingValues;

        return $this;
    }

    public function setAddCasesInMonitoring(?bool $addCasesInMonitoring): static
    {
        $this->addCasesInMonitoring = $addCasesInMonitoring;

        return $this;
    }

    protected function makeComposedReportService(): BeneficiariesV2
    {
        $service = new BeneficiariesV2;
        $service->setReportType($this->reportType)
            ->setStartDate($this->startDate)
            ->setEndDate($this->endDate)
            ->setShowMissingValue($this->showMissingValues)
            ->setAddCasesInMonitoring($this->addCasesInMonitoring)
            ->composeReport();

        return $service;
    }
}
