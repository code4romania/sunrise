<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Models\Monitoring;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class MonitoringResource extends Resource
{
    protected static ?string $model = Monitoring::class;

    protected static bool $shouldRegisterNavigation = false;

    public static string $parentResource = BeneficiaryResource::class;

    public static function getRecordTitle(Model|null $record): string|null|Htmlable
    {
        return $record->number;
    }
}
