<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBeneficiaries;
use App\Concerns\HasLocation;
use App\Concerns\HasSlug;
use App\Concerns\HasUlid;
use App\Enums\OrganizationType;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Organization extends Model implements HasAvatar, HasMedia, HasName, HasCurrentTenantLabel
{
    use HasBeneficiaries;
    use HasFactory;
    use HasLocation;
    use HasUlid;
    use HasSlug;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'short_name',
        'main_activity',
    ];

    protected $casts = [
        'type' => OrganizationType::class,
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_organizations');
    }

    public function admins(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_organizations')
            ->where('ngo_admin', 1);
    }

    public function organizationServices(): HasMany
    {
        return $this->hasMany(OrganizationService::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }

    public function communityProfile(): HasOne
    {
        return $this->hasOne(CommunityProfile::class)
            ->withDefault(function (CommunityProfile $communityProfile, Organization $organization) {
                $communityProfile->name = $organization->name;
            });
    }

    public function monitorings()
    {
        return $this->hasManyThrough(Monitoring::class, Beneficiary::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
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

    public function getFilamentName(): string
    {
        return $this->short_name ?: $this->name;
    }

    public function getCurrentTenantLabel(): string
    {
        return $this->name;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('logo', 'thumb');
    }

    protected function getSlugSource(): string
    {
        return $this->getFilamentName();
    }
}
