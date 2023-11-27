<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUlid
{
    protected $ulidColumn = 'ulid';

    public static function bootHasUlid()
    {
        // Generate ULID if none provided
        static::creating(function (Model $model) {
            if (! $model->{$model->ulidColumn}) {
                $model->{$model->ulidColumn} = (string) Str::ulid();
            }
        });

        // Make sure ULIDs can't be changed
        static::updating(function (Model $model) {
            $originalUlid = $model->getOriginal($model->ulidColumn);

            if (
                ! \is_null($originalUlid) &&
                $originalUlid !== $model->{$model->ulidColumn}
            ) {
                $model->{$model->ulidColumn} = $originalUlid;
            }
        });
    }
}
