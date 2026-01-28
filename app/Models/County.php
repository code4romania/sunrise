<?php

declare(strict_types=1);

namespace App\Models;

use Str;
use App\Models\Scopes\AlphabeticalOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class County extends Model
{
    use HasFactory;
    use Searchable;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'siruta',
        'name',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AlphabeticalOrder);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
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
                ],
            ],
            'search-parameters' => [
                'query_by' => 'name,aliases',
            ],
        ];
    }
}
