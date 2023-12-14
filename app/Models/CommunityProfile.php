<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasCounties;
use App\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CommunityProfile extends Model implements HasMedia
{
    use HasCounties;
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'email',
        'phone',
        'website',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(ServicePivot::class, 'model_id')
            ->where('model_type', $this->getMorphClass());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('large')
                    ->fit(Manipulations::FIT_CONTAIN, 256, 256)
                    ->optimize();
            });
    }

    protected function getSlugSource(): string
    {
        return $this->name;
    }
}
