<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBeneficiaries;
use App\Concerns\HasLocation;
use App\Concerns\HasSlug;
use App\Concerns\HasUlid;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Image\Manipulations;
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

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_organizations');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function communityProfile(): HasOne
    {
        return $this->hasOne(CommunityProfile::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->fit(Manipulations::FIT_CONTAIN, 64, 64)
                    ->optimize();

                $this->addMediaConversion('large')
                    ->fit(Manipulations::FIT_CONTAIN, 256, 256)
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
