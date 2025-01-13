<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\AlphabeticalOrder;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'level',
        'type',
        'name',
        'county_id',
        'parent_id',
    ];

    protected $with = [
        'parent',
    ];

    protected $appends = [
        'name_with_uat',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AlphabeticalOrder);
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query
            ->with('parent')
            ->where(function (Builder $query) use ($search) {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhereHas('parent', function (Builder $query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            })
            ->whereNot('level', 2);
    }

    public function getParentNameAttribute(): ?string
    {
        if (
            $this->parent_id !== null
        ) {
            $parentName = $this->parent->name;

            return $this->parent->type === 3 ? \sprintf('%s %s', __('general.labels.commune'), $parentName) : $parentName;
        }

        return null;
    }

    public function getNameWithUatAttribute(): ?string
    {
        $name = $this->type == 22 ? \sprintf('%s - %s', $this->name, __('general.labels.village')) : $this->name;

        return $this->parent_name ? \sprintf('%s (%s)', $name, $this->parent_name) : $name;
    }
}
