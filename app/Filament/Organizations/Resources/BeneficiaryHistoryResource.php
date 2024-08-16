<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Models\Activity;
use App\Models\Beneficiary;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class BeneficiaryHistoryResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static bool $shouldRegisterNavigation = false;

    public static string $parentResource = BeneficiaryResource::class;

    public static function getTenantOwnershipRelationship(Beneficiary|Model $record): Relation
    {
        $parent = Beneficiary::find(request('parent'));

        return $parent->organization();
    }

    public static function resolveRecordRouteBinding(int | string $key): ?Model
    {
        return app(static::getModel())
            ->resolveRouteBindingQuery(static::getEloquentQuery(), $key, static::getRecordRouteKeyName())
            ->first();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

//        if (
//            static::isScopedToTenant() &&
//            ($tenant = Filament::getTenant())
//        ) {
//            static::scopeEloquentQueryToTenant($query, $tenant);
//        }

        return $query;
    }

    public static function getRecordTitle(Model|null $record): string|null|Htmlable
    {
        return $record->subject_type;
    }
}
