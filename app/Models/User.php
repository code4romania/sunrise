<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUlid;
use App\Concerns\HasUserStatus;
use App\Concerns\MustSetInitialPassword;
use App\Enums\CasePermission;
use App\Enums\UserStatus;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasName, HasMedia, HasTenants, HasDefaultTenant
{
    use CausesActivity;
    use HasApiTokens;
    use HasFactory;
    use HasUlid;
    use InteractsWithMedia;
    use LogsActivity;
    use MustSetInitialPassword;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasUserStatus;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'status',
        'password',
        'password_set_at',
        'latest_organization_id',
        'is_admin',
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
        'password_set_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'status' => UserStatus::class,
    ];

    protected static function booted()
    {
        static::addGlobalScope('withLastLogin', function (Builder $query) {
            return $query->withLastLoginAt();
        });

        static::creating(function (User $model) {
            $model->setPendingStatus();
        });
    }

    public function organizations(): MorphToMany
    {
        return $this->morphToMany(Organization::class, 'model', 'model_has_organizations', 'model_id');
    }

    public function latestOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'latest_organization_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->using(UserRole::class)
            ->withPivot(['organization_id']);
    }

    public function rolesInOrganization(): BelongsToMany
    {
        return $this->roles()
            ->wherePivot('organization_id', Filament::getTenant()?->id)
            ->active();
    }

    public function permissions(): HasOne
    {
        return $this->hasOne(OrganizationUserPermissions::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logExcept($this->hidden)
            ->logOnlyDirty();
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

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->latestOrganization ?? $this->getTenants($panel)->first();
    }

    public function scopeWithLastLoginAt(Builder $query): Builder
    {
        return $query
            ->addSelect([
                'last_login_at' => Activity::query()
                    ->select('created_at')
                    ->where('subject_type', $this->getMorphClass())
                    ->whereColumn('subject_id', 'users.id')
                    ->where('log_name', 'system')
                    ->where('event', 'logged_in')
                    ->latest()
                    ->take(1),
            ])
            ->withCasts(['last_login_at' => 'datetime']);
    }

    // TODO create notifications
    public function resetPassword(): void
    {
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public static function getTenantOrganizationUsers(): Collection
    {
        return Filament::getTenant()
            ->users
            ->pluck('full_name', 'id');
    }

    public function canBeCaseManager(): bool
    {
        return $this->permissions->case_permissions->contains(CasePermission::CAN_BE_CASE_MANAGER);
    }

    public function hasRoleInOrganization(Role | int $role): bool
    {
        $roleID = \is_int($role) ? $role : $role->id;
        foreach ($this->rolesInOrganization as $roleInOrganization) {
            if ($roleInOrganization->id === $roleID) {
                return true;
            }
        }

        return false;
    }
}
