<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class Document extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use BelongsToBeneficiary;
    use BelongsToThroughTrait;

    protected $fillable = [
        'date',
        'type',
        'name',
        'observations',
    ];

    protected $casts = [
        'type' => DocumentType::class,
    ];

    public function organization(): BelongsToThrough
    {
        return $this->belongsToThrough(Organization::class, Beneficiary::class);
    }
}
