<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasCounties;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CommunityProfile extends Model implements HasMedia
{
    use HasCounties;
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'email',
        'phone',
        'website',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
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
}
