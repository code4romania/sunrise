<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Support;

use Illuminate\Support\Str;

class ExportFilenameBuilder
{
    public function build(string $reportName, int|string $caseId, string $extension): string
    {
        $normalizedReportName = (string) Str::of($reportName)
            ->ascii()
            ->upper()
            ->replaceMatches('/[^A-Z0-9]+/', '_')
            ->trim('_');

        $date = now()->format('Y-m-d');

        return "{$normalizedReportName}_{$caseId}_{$date}.{$extension}";
    }
}
