<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use App\Enums\RecommendationService;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailedEvaluationResult extends Model
{
    use BelongsToBeneficiary;
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'recommendation_services',
        'other_services_description',
        'recommendations_for_intervention_plan',
    ];

    protected $casts = [
        'recommendation_services' => AsEnumCollection::class.':'.RecommendationService::class,
    ];
}
