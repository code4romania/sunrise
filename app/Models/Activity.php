<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActivityDescription;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity as BaseActivity;

class Activity extends BaseActivity
{
    public const UPDATED_AT = null;

    /**
     * The relationships that should always be loaded.
     *
     * @var string[]
     */
    protected $with = [
        'causer', 'subject',
    ];

    protected $casts = [
        'properties' => 'collection',
        'description' => ActivityDescription::class,
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('latest', function (Builder $query) {
            return $query->latest();
        });
    }

    public function scopeBetweenDates(Builder $query, ?string $from = null, ?string $until = null): Builder
    {
        return $query
            ->when($from, function (Builder $query, string $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($until, function (Builder $query, string $date) {
                $query->whereDate('created_at', '<=', $date);
            });
    }

    public function organization()
    {
        if (method_exists($this->subject, 'organization')) {
            return $this->subject->organization();
        }

        return null;
    }
}
