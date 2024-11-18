<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Concerns\HasCaseStatus;
use App\Concerns\HasCitizenship;
use App\Concerns\HasEffectiveAddress;
use App\Concerns\HasEthnicity;
use App\Concerns\HasSpecialistsTeam;
use App\Concerns\HasUlid;
use App\Concerns\LogsActivityOptions;
use App\Enums\CaseStatus;
use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Enums\IDType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
    use HasSpecialistsTeam;

    protected $fillable = [
        'initial_id',
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
        'children_notes',

        'notes'

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
        'civil_status' => CivilStatus::class,
        'doesnt_have_children' => 'boolean',
        'gender' => Gender::class,
        'same_as_legal_residence' => 'boolean',
        'status' => CaseStatus::class,
    ];

    protected static function booted()
    {
        static::created(function (Beneficiary $beneficiary) {
            if (auth()->guest()) {
                return;
            }

            /** @var User $user */
            $user = auth()->user();
            $beneficiary->specialistsTeam()->create([
                'user_id' => $user->id,
                'role_id' => $user->canBeCaseManager()
                    ? $user->rolesInOrganization
                        ->filter(fn ($role) => $role->case_manager)
                        ->first()
                        ?->id
                    : $user->rolesInOrganization
                        ->first()
                        ?->id,
                'specialistable_type' => $beneficiary->getMorphClass(),
            ]);
        });
    }

    public function scopeWhereUserHasAccess(Builder $query): Builder
    {
        $user = auth()->user();
        if ($user->isNgoAdmin() || $user->hasAccessToAllCases()) {
            return $query;
        }

        $query->whereHas('specialistsMembers', fn (Builder $query) => $query->where('users.id', $user->id));

        return $query;
    }

    public function getBreadcrumb(): string
    {
        return \sprintf('#%d %s', $this->id, $this->full_name);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Children::class);
    }

    public function aggressors(): HasMany
    {
        return $this->hasMany(Aggressor::class);
    }

    public function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birthdate?->age,
        );
    }

    public function detailedEvaluationSpecialists(): HasMany
    {
        return $this->hasMany(DetailedEvaluationSpecialist::class);
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

    public function managerTeam(): HasMany
    {
        return $this->specialistsTeam()
            ->whereHas(
                'role',
                fn (Builder $query) => $query->where('case_manager', true)
            );
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

    public function lastMonitoring(): HasOne
    {
        return $this->hasOne(Monitoring::class)->orderByDesc('date');
    }

    public function closeFile(): HasOne
    {
        return $this->hasOne(CloseFile::class);
    }

    public function interventionPlan(): HasOne
    {
        return $this->hasOne(InterventionPlan::class);
    }

    public function antecedents(): HasOne
    {
        return $this->hasOne(BeneficiaryAntecedents::class);
    }

    public function flowPresentation(): HasOne
    {
        return $this->hasOne(FlowPresentation::class);
    }

    public function details(): HasOne
    {
        return $this->hasOne(BeneficiaryDetails::class);
    }
}
