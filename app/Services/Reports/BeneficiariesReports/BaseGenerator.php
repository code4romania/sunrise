<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\ActivityDescription;
use App\Enums\CaseStatus;
use App\Models\Beneficiary;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

abstract class BaseGenerator
{
    protected string | null $startDate = null;

    protected string | null $endDate = null;

    protected bool | null $showMissingValues = false;

    protected bool | null $addCasesInMonitoring = false;

    protected $query;

    public function __construct()
    {
        $this->query = Beneficiary::query()
            ->selectRaw('COUNT(*) as total_cases');
    }

    public function setStartDate(?string $startDate): self
    {
        $this->startDate = $startDate ? Carbon::parse($startDate)->startOfDay()->format('Y-m-d H:i:s') : null;

        return $this;
    }

    public function setEndDate(?string $endDate): self
    {
        $this->endDate = $endDate ?
            Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s') :
            Carbon::now()->endOfDay()->format('Y-m-d H:i:s');

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

    public function getReportData(): Collection
    {
        $this->setSelectedFields();
        $this->addConditions();
        $this->addRelatedTables();
        $this->query
            ->groupBy($this->getGroupBy());

        return $this->query
            ->toBase()
            ->get();
    }

    private function setSelectedFields(): void
    {
        $selectedFields = $this->getSelectedFields();
        if (\is_array($selectedFields)) {
            foreach ($selectedFields as $field) {
                $this->query->selectRaw($field);
            }

            return;
        }

        $this->query->selectRaw($selectedFields);
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

        $this->addDateConditions();
    }

    public function addDateConditions(): void
    {
        $this->query
            ->when(
                $this->startDate,
                fn (EloquentBuilder $query) => $query
                    ->whereHas(
                        'activity',
                        fn (EloquentBuilder $query) => $query
                            ->where(
                                fn (EloquentBuilder $query) => $query->whereJsonContains('properties->attributes->status', CaseStatus::ACTIVE->value)
                                    ->when(
                                        $this->addCasesInMonitoring,
                                        fn (EloquentBuilder $query) => $query->orWhereJsonContains('properties->attributes->status', CaseStatus::MONITORED->value)
                                    )
                            )
                            ->where('created_at', '<=', $this->endDate)
                            ->whereIn('activity_log.description', [ActivityDescription::CREATED->value, ActivityDescription::UPDATED->value])
                            ->whereNotExists(
                                fn (Builder $subQuery) => $subQuery->select(DB::raw(1))
                                    ->from('activity_log as sublog')
                                    ->whereColumn('sublog.subject_id', 'activity_log.subject_id')
                                    ->whereIn('sublog.description', [
                                        ActivityDescription::CREATED->value,
                                        ActivityDescription::UPDATED->value,
                                    ])
                                    ->where('sublog.subject_type', 'beneficiary')
                                    ->whereColumn('sublog.created_at', '>', 'activity_log.created_at')
                                    ->whereJsonContainsKey('properties->attributes->status')
                                    ->where('sublog.created_at', '<=', $this->endDate)
                            )
                    )
                    ->orWhereHas(
                        'activity',
                        fn (EloquentBuilder $query) => $query->whereJsonContains('properties->old->status', CaseStatus::ACTIVE->value)
                            ->when(
                                $this->addCasesInMonitoring,
                                fn (EloquentBuilder $query) => $query->orWhereJsonContains('properties->old->status', CaseStatus::MONITORED->value)
                            )
                            ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    )
            )
            ->when(
                ! $this->startDate,
                fn (EloquentBuilder $query) => $query
                    ->whereHas(
                        'activity',
                        fn (EloquentBuilder $query) => $query
                            ->where(
                                fn (EloquentBuilder $query) => $query
                                    ->whereJsonContains('properties->attributes->status', CaseStatus::ACTIVE->value)
                                    ->when(
                                        $this->addCasesInMonitoring,
                                        fn (EloquentBuilder $query) => $query->orWhereJsonContains('properties->attributes->status', CaseStatus::MONITORED->value)
                                    )
                            )
                            ->whereIn('description', [ActivityDescription::CREATED->value, ActivityDescription::UPDATED->value])
                            ->where('created_at', '<=', $this->endDate)
                            ->where('subject_type', 'beneficiary')
                    )
            );
    }

    public function addRelatedTables(): void
    {
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
