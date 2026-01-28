<?php

declare(strict_types=1);

namespace App\Models;

use Str;
use App\Models\Scopes\AlphabeticalOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class City extends Model
{
    use HasFactory;
    use Searchable;

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

    public function shouldBeSearchable(): bool
    {
        return $this->level === 3;
    }

    public function toSearchableArray(): array
    {
        $this->loadMissing('county');

        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'county_id' => (string) $this->county_id,
            'aliases' => $this->getAliases(),
        ];
    }

    public function getAliases(): array
    {
        $aliases = [];

        if (Str::of($this->name)->contains('Târgul')) {
            $aliases[] = Str::of($this->name)->replace('Târgul', 'Tg')->toString();
            $aliases[] = Str::of($this->name)->replace('Târgul', 'Târgu')->toString();
            $aliases[] = Str::of($this->name)->replace('Târgul', 'Tirgul')->toString();
            $aliases[] = Str::of($this->name)->replace('Târgul', 'Tirgu')->toString();
        }

        if (Str::of($this->name)->contains('Târgu')) {
            $aliases[] = Str::of($this->name)->replace('Târgu', 'Tg')->toString();
            $aliases[] = Str::of($this->name)->replace('Târgu', 'Târgul')->toString();
            $aliases[] = Str::of($this->name)->replace('Târgu', 'Tirgu')->toString();
            $aliases[] = Str::of($this->name)->replace('Târgu', 'Tirgul')->toString();
        }

        if (Str::of($this->name)->contains('Satu')) {
            $aliases[] = Str::of($this->name)->replace('Satu', 'Sat')->toString();
            $aliases[] = Str::of($this->name)->replace('Satu', 'Satu Mare')->toString();
        }

        return $aliases;
    }

    public static function getTypesenseModelSettings(): array
    {
        return [
            'collection-schema' => [
                'fields' => [
                    [
                        'name' => 'id',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'name',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'aliases',
                        'type' => 'string[]',
                    ],
                    [
                        'name' => 'county_id',
                        'type' => 'string',
                    ],
                ],
            ],
            'search-parameters' => [
                'query_by' => 'name,aliases',
            ],
        ];
    }
}
