<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;

class Organization extends Model implements HasAvatar
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar_url',
    ];

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_organizations');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }
}
