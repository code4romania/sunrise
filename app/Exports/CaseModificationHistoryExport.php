<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Activity;
use App\Services\ActivityLabelHelper;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CaseModificationHistoryExport implements FromCollection, WithHeadings
{
    /**
     * @param  Collection<int, Activity>  $activities
     */
    public function __construct(
        private readonly Collection $activities
    ) {}

    public function collection(): Collection
    {
        return $this->activities->map(function (Activity $activity): array {
            return [
                $activity->created_at?->format('d.m.Y') ?? '',
                $activity->created_at?->format('H:i') ?? '',
                $activity->causer?->full_name ?? '',
                $activity->description?->getLabel() ?? $activity->description?->value ?? '',
                ActivityLabelHelper::getEventLabel($activity),
                ActivityLabelHelper::getSubsectionLabel($activity),
            ];
        });
    }

    public function headings(): array
    {
        return [
            __('beneficiary.section.history.labels.date'),
            __('beneficiary.section.history.labels.time'),
            __('beneficiary.section.history.labels.user'),
            __('beneficiary.section.history.labels.description'),
            __('beneficiary.section.history.labels.section'),
            __('beneficiary.section.history.labels.subsection'),
        ];
    }
}
