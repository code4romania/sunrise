<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Support;

use App\Models\Beneficiary;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

final class BeneficiaryDetailsPanelFormatter
{
    public static function formatDateState(mixed $state): string
    {
        if ($state === null || $state === '' || $state === '-') {
            return '—';
        }

        try {
            return Carbon::parse($state)->translatedFormat('d.m.Y');
        } catch (\Throwable) {
            return '—';
        }
    }

    public static function formatAddress(Beneficiary $record): string
    {
        $addr = $record->effective_residence;
        if (! $addr) {
            return '';
        }
        $parts = array_filter([
            $addr->address,
            $addr->city?->name,
            $addr->county ? __('field.county').' '.$addr->county->name : null,
        ]);

        return implode(', ', $parts);
    }

    public static function formatEnumLabel(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return \is_object($value) && method_exists($value, 'getLabel')
            ? (string) $value->getLabel()
            : (string) $value;
    }

    public static function formatCollectionLabels(mixed $values): string
    {
        if ($values instanceof Collection || $values instanceof Arrayable || \is_array($values)) {
            $items = collect($values)
                ->map(fn (mixed $item): string => self::formatEnumLabel($item))
                ->filter(fn (string $item): bool => $item !== '—')
                ->values();

            return $items->isNotEmpty() ? $items->implode('; ') : '—';
        }

        return self::formatEnumLabel($values);
    }
}
