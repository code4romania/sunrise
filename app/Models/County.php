<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\AlphabeticalOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class County extends Model
{
    use HasFactory;

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
}
