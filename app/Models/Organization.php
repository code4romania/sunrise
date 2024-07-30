<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBeneficiaries;
use App\Concerns\HasLocation;
use App\Concerns\HasSlug;
use App\Concerns\HasUlid;
use App\Database\Eloquent\Relations\HasManyThrough;
use App\Enums\OrganizationType;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'type',
        'cif',
        'main_activity',
        'address',
        'reprezentative_name',
        'reprezentative_email',
        'phone',
        'website',
    ];

    protected $casts = [
        'type' => OrganizationType::class,
    ];

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_organizations');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }

    public function documents(): HasManyThrough
    {
        return $this->hasManyThrough(Document::class, Beneficiary::class);
    }

    protected function newHasManyThrough(Builder $query, Model $farParent, Model $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey): HasManyThrough
    {
        return new HasManyThrough($query, $farParent, $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey);
    }

    public function communityProfile(): HasOne
    {
        return $this->hasOne(CommunityProfile::class)
            ->withDefault(function (CommunityProfile $communityProfile, Organization $organization) {
                $communityProfile->name = $organization->name;
            });
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
        return $this->short_name;
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
