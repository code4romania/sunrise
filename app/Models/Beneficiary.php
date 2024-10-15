<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Concerns\HasCaseStatus;
use App\Concerns\HasCitizenship;
use App\Concerns\HasEffectiveAddress;
use App\Concerns\HasEthnicity;
use App\Concerns\HasUlid;
use App\Concerns\LogsActivityOptions;
use App\Enums\ActLocation;
use App\Enums\CaseStatus;
use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Enums\HomeOwnership;
use App\Enums\IDType;
use App\Enums\Income;
use App\Enums\NotificationMode;
use App\Enums\Notifier;
use App\Enums\Occupation;
use App\Enums\PresentationMode;
use App\Enums\ReferralMode;
use App\Enums\ResidenceEnvironment;
use App\Enums\Role;
use App\Enums\Studies;
use App\Enums\Ternary;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Beneficiary extends Model
{
    use BelongsToOrganization;
    use HasCaseStatus;
    use HasCitizenship;
    use HasEthnicity;
    use HasFactory;
    use HasUlid;
    use HasEffectiveAddress;
    use LogsActivity;
    use LogsActivityOptions;

    protected $fillable = [
        'first_name',
        'last_name',
        'prior_name',

        'civil_status',
        'cnp',
        'gender',

        'birthdate',
        'birthplace',
        'ethnicity',

        'id_type',
        'id_serial',
        'id_number',

        'legal_residence_environment',
        'effective_residence_environment',

        'primary_phone',
        'backup_phone',
        'email',
        'contact_notes',

        'status',

        'doesnt_have_children',
        'children_total_count',
        'children_care_count',
        'children_under_10_care_count',
        'children_10_18_care_count',
        'children_18_care_count',
        'children_accompanying_count',
        'children',
        'children_notes',

        'has_family_doctor',
        'family_doctor_name',
        'family_doctor_contact',
        'psychiatric_history',
        'psychiatric_history_notes',
        'criminal_history',
        'criminal_history_notes',
        'studies',
        'occupation',
        'workplace',
        'income',
        'elder_care_count',
        'homeownership',

        'has_police_reports',
        'police_report_count',
        'has_medical_reports',
        'medical_report_count',

        'presentation_mode',
        'referral_mode',
        'notification_mode',
        'notifier',
        'notifier_other',

        'act_location',
        'act_location_other',
    ];

    protected $casts = [
        'id_type' => IDType::class,
        'birthdate' => 'date',
        'children_10_18_care_count' => 'integer',
        'children_18_care_count' => 'integer',
        'children_accompanying_count' => 'integer',
        'children_care_count' => 'integer',
        'children_total_count' => 'integer',
        'children_under_10_care_count' => 'integer',
        'children' => 'collection',
        'civil_status' => CivilStatus::class,
        'criminal_history' => Ternary::class,
        'doesnt_have_children' => 'boolean',
        'effective_residence_environment' => ResidenceEnvironment::class,
        'legal_residence_environment' => ResidenceEnvironment::class,
        'elder_care_count' => 'integer',
        'gender' => Gender::class,
        'has_family_doctor' => Ternary::class,
        'has_medical_reports' => Ternary::class,
        'has_police_reports' => Ternary::class,
        'homeownership' => HomeOwnership::class,
        'income' => Income::class,
        'occupation' => Occupation::class,
        'psychiatric_history' => Ternary::class,
        'same_as_legal_residence' => 'boolean',
        'status' => CaseStatus::class,
        'studies' => Studies::class,
        'presentation_mode' => PresentationMode::class,
        'referral_mode' => ReferralMode::class,
        'notification_mode' => NotificationMode::class,
        'notifier' => Notifier::class,
        'act_location' => AsEnumCollection::class . ':' . ActLocation::class,
    ];

    protected static function booted()
    {
        static::created(function (Beneficiary $beneficiary) {
            if (auth()->guest()) {
                return;
            }

            $user = auth()->user();
            $beneficiary->team()->create([
                'user_id' => $user->id,
                'roles' => $user->can_be_case_manager
                    ? [Role::MANGER]
                    : $user->roles,
            ]);
        });
    }

    public function getBreadcrumb(): string
    {
        return sprintf('#%d %s', $this->id, $this->full_name);
    }

    public function aggressor(): HasMany
    {
        return $this->hasMany(Aggressor::class);
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

    public function referringInstitution(): BelongsTo
    {
        return $this->belongsTo(ReferringInstitution::class)
            ->orderBy('order');
    }

    public function firstCalledInstitution(): BelongsTo
    {
        return $this->belongsTo(ReferringInstitution::class, 'first_called_institution_id')
            ->orderBy('order');
    }

    public function otherCalledInstitution()
    {
        return $this->institutions();
    }

    public function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birthdate?->age,
        );
    }

    public function specialists(): HasMany
    {
        return $this->hasMany(Specialist::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function partner(): HasOne
    {
        return $this->hasOne(BeneficiaryPartner::class);
    }

    public function multidisciplinaryEvaluation(): HasOne
    {
        return $this->hasOne(MultidisciplinaryEvaluation::class);
    }

    public function detailedEvaluationResult(): HasOne
    {
        return $this->hasOne(DetailedEvaluationResult::class);
    }

    public function evaluateDetails(): HasOne
    {
        return $this->hasOne(EvaluateDetails::class);
    }

    public function violence(): HasOne
    {
        return $this->hasOne(Violence::class);
    }

    public function riskFactors(): HasOne
    {
        return $this->hasOne(RiskFactors::class);
    }

    public function requestedServices(): HasOne
    {
        return $this->hasOne(RequestedServices::class);
    }

    public function beneficiarySituation(): HasOne
    {
        return $this->hasOne(BeneficiarySituation::class);
    }

    public function team(): HasMany
    {
        return $this->hasMany(CaseTeam::class);
    }

    public function violenceHistory(): HasMany
    {
        return $this->hasMany(ViolenceHistory::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function monitoring(): HasMany
    {
        return $this->hasMany(Monitoring::class);
    }

    public function closeFile(): HasOne
    {
        return $this->hasOne(CloseFile::class);
    }
}
