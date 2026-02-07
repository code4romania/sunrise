<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use App\Enums\RecommendationService;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestedServices extends Model
{
    use BelongsToBeneficiary;
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'requested_services',
        'other_services_description',
    ];

    protected $casts = [
        'requested_services' => AsEnumCollection::class.':'.RecommendationService::class,
    ];
}
