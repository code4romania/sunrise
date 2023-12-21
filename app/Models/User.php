<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Concerns\HasUlid;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasName, HasMedia, HasTenants
{
    use HasApiTokens;
    use HasFactory;
    use HasUlid;
    use InteractsWithMedia;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    public function organizations(): MorphToMany
    {
        return $this->morphToMany(Organization::class, 'model', 'model_has_organizations', 'model_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Contain, 64, 64)
                    ->keepOriginalImageFormat()
                    ->optimize();

                $this->addMediaConversion('large')
                    ->fit(Fit::Contain, 256, 256)
                    ->keepOriginalImageFormat()
                    ->optimize();
            });
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('avatar', 'thumb');
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->is_admin;
        }

        return $this->getTenants($panel)->isNotEmpty();
    }

    public function getTenants(Panel $panel): Collection
    {
        if ($panel->getId() === 'organization') {
            return $this->organizations;
        }

        return collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->organizations->contains($tenant);
    }
}
