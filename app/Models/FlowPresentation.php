<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use App\Enums\ActLocation;
use App\Enums\NotificationMode;
use App\Enums\Notifier;
use App\Enums\PresentationMode;
use App\Enums\ReferralMode;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class FlowPresentation extends Model
{
    use BelongsToBeneficiary;
    use HasFactory;
    use LogsActivityOptions;

    protected $fillable = [
        'presentation_mode',
        'referring_institution_id',
        'referral_mode',
        'notifier',
        'notification_mode',
        'notifier_other',

        'act_location',
        'act_location_other',
    ];

    protected $casts = [
        'presentation_mode' => PresentationMode::class,
        'referral_mode' => AsEnumCollection::class.':'.ReferralMode::class,
        'notification_mode' => NotificationMode::class,
        'notifier' => Notifier::class,
        'act_location' => AsEnumCollection::class.':'.ActLocation::class,
    ];

    public function firstCalledInstitution(): BelongsTo
    {
        return $this->belongsTo(ReferringInstitution::class, 'first_called_institution_id')
            ->orderBy('order');
    }

    public function institutions(): MorphToMany
    {
        return $this->morphToMany(
            ReferringInstitution::class,
            'model',
            'model_has_referring_institutions',
            relatedPivotKey: 'institution_id'
        )

            ->orderBy('order');
    }

    public function otherCalledInstitution(): MorphToMany
    {
        return $this->institutions();
    }

    public function referringInstitution(): BelongsTo
    {
        return $this->belongsTo(ReferringInstitution::class)
            ->orderBy('order');
    }
}
