<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Models\Beneficiary;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

abstract class BaseGenerator
{
    protected string | null $startDate;

    protected string | null $endDate = null;

    protected bool | null $showMissingValues = false;

    protected $query;

    public function __construct()
    {
        $this->query = Beneficiary::query()
            ->leftJoin('close_files', 'beneficiaries.id', '=', 'close_files.beneficiary_id')
            ->toBase()
            ->selectRaw('COUNT(*) as total_cases');
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

    public function getReportData(): Collection
    {
        $this->query
            ->selectRaw($this->getSelectedFields())
            ->groupBy($this->getGroupBy());

        $this->addConditions();

        return $this->query
            ->get();
    }

    public function addConditions(): void
    {
        if (! $this->showMissingValues) {
            if ($this->getHorizontalSubHeaderKey()) {
                $this->query->whereNotNull($this->getFieldNameForHeaderKey($this->getHorizontalSubHeaderKey()));
            }

            if ($this->getVerticalHeaderKey()) {
                $this->query->whereNotNull($this->getFieldNameForHeaderKey($this->getVerticalHeaderKey()));
            }

            if ($this->getVerticalSubHeaderKey()) {
                $this->query->whereNotNull($this->getFieldNameForHeaderKey($this->getVerticalSubHeaderKey()));
            }
        }

        if ($this->endDate) {
            $this->query->where('beneficiaries.created_at', '<=', $this->endDate . ' 23:59:59');
        }

        if ($this->startDate) {
            $this->query->where(
                fn (Builder $query) => $query->where('close_files.date', '>=', $this->startDate)
                    ->orWhereNull('close_files.date')
            );
        }
    }

    protected function getGroupBy(): array
    {
        return array_filter([
            $this->getHorizontalSubHeaderKey(),
            $this->getVerticalHeaderKey(),
            $this->getVerticalSubHeaderKey(),
        ]);
    }

    private function getFieldNameForHeaderKey(string $headerKey): string
    {
        if ($headerKey === 'age_group') {
            return 'birthdate';
        }

        if ($headerKey === 'gender') {
            return 'beneficiaries.gender';
        }

        return $headerKey;
    }

    public function getHorizontalSubHeader(): ?array
    {
        return [];
    }

    public function getVerticalSubHeader(): ?array
    {
        return [];
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return null;
    }

    public function getVerticalSubHeaderKey(): ?string
    {
        return null;
    }
}
