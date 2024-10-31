<?php

declare(strict_types=1);

namespace App\Services\Reports;

use App\Enums\ReportType;
use App\Interfaces\ReportGenerator;
use Illuminate\Support\Collection;

class BeneficiariesV2
{
    protected ReportType | string | null $reportType;

    protected ReportGenerator $generator;

    protected string | null $startDate = null;

    protected string | null $endDate = null;

    protected bool | null $showMissingValue = false;

    public function setReportType(ReportType | string | null $reportType): self
    {
        $this->reportType = $reportType;

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

    public function setShowMissingValue(?bool $showMissingValue): self
    {
        $this->showMissingValue = $showMissingValue;

        return $this;
    }

    public function composeReport(): void
    {
        $generatorClass = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->reportType->value)));
        $generatorClass = 'App\\Services\\Reports\\BeneficiariesReports\\' . $generatorClass;

        $this->generator = new $generatorClass();
        $this->generator
            ->setStartDate($this->startDate)
            ->setEndDate($this->endDate)
            ->setShowMissingValues($this->showMissingValue);
    }

    public function getReportData(): Collection
    {
        return $this->generator->getReportData();
    }

    public function getHorizontalHeader(): array
    {
        return $this->generator->getHorizontalHeader();
    }

    public function getHorizontalSubHeader(): array
    {
        return $this->generator->getHorizontalSubHeader();
    }

    public function getSubHeaderKey(): ?string
    {
        return $this->generator->getHorizontalSubHeaderKey();
    }

    public function getVerticalHeader(): array
    {
        return $this->generator->getVerticalHeader();
    }

    public function getVerticalHeaderKey(): ?string
    {
        return $this->generator->getVerticalHeaderKey();
    }

    public function getVerticalSubHeader(): array
    {
        return $this->generator->getVerticalSubHeader();
    }

    public function getVerticalSubHeaderKey(): ?string
    {
        return $this->generator->getVerticalSubHeaderKey();
    }
}
