<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Collection;

final class Utf8ForDompdf
{
    public static function scrubString(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (function_exists('mb_scrub')) {
            return mb_scrub($value, 'UTF-8');
        }

        $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $value);

        return $clean !== false ? $clean : '';
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function scrubReportStatisticsViewData(array $data): array
    {
        foreach (['title', 'exportPeriodStart', 'exportPeriodEnd'] as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $data[$key] = self::scrubString($data[$key]);
            }
        }

        foreach (['header', 'subHeader', 'verticalHeader', 'verticalSubHeader'] as $arrayKey) {
            if (! isset($data[$arrayKey]) || ! is_array($data[$arrayKey])) {
                continue;
            }

            $data[$arrayKey] = array_map(
                fn (mixed $cell): mixed => is_string($cell) ? self::scrubString($cell) : $cell,
                $data[$arrayKey]
            );
        }

        if (isset($data['reportData']) && $data['reportData'] instanceof Collection) {
            $data['reportData'] = $data['reportData']->map(function (mixed $row): mixed {
                if (! is_object($row)) {
                    return $row;
                }

                $clone = clone $row;
                foreach (get_object_vars($clone) as $k => $v) {
                    if (is_string($v)) {
                        $clone->{$k} = self::scrubString($v);
                    }
                }

                return $clone;
            });
        }

        return $data;
    }
}
