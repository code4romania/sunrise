<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Models\Activity;
use App\Models\Address;
use App\Models\Aggressor;
use App\Models\Beneficiary;
use App\Models\BeneficiaryAntecedents;
use App\Models\BeneficiaryDetails;
use App\Models\BeneficiaryIntervention;
use App\Models\BeneficiaryPartner;
use App\Models\BeneficiarySituation;
use App\Models\Benefit;
use App\Models\BenefitService;
use App\Models\BenefitType;
use App\Models\Children;
use App\Models\City;
use App\Models\CloseFile;
use App\Models\CommunityProfile;
use App\Models\Country;
use App\Models\County;
use App\Models\DetailedEvaluationResult;
use App\Models\DetailedEvaluationSpecialist;
use App\Models\Document;
use App\Models\EvaluateDetails;
use App\Models\FlowPresentation;
use App\Models\Institution;
use App\Models\InterventionMeeting;
use App\Models\InterventionPlan;
use App\Models\InterventionPlanResult;
use App\Models\InterventionService;
use App\Models\Meeting;
use App\Models\Monitoring;
use App\Models\MonitoringChild;
use App\Models\MonthlyPlan;
use App\Models\MonthlyPlanInterventions;
use App\Models\MonthlyPlanService;
use App\Models\MultidisciplinaryEvaluation;
use App\Models\Organization;
use App\Models\OrganizationService;
use App\Models\OrganizationServiceIntervention;
use App\Models\OrganizationUserPermissions;
use App\Models\ReferringInstitution;
use App\Models\RequestedServices;
use App\Models\Result;
use App\Models\RiskFactors;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceCounselingSheet;
use App\Models\ServiceIntervention;
use App\Models\ServicePivot;
use App\Models\Specialist;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserStatus;
use App\Models\Violence;
use App\Models\ViolenceHistory;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerBlueprintMacros();
        $this->registerViteMacros();

        $this->app->bind(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class, LoginResponse::class);

        Table::configureUsing(function (Table $table) {
            return $table->defaultSort('created_at', 'desc');
        });

        Column::macro('shrink', fn () => $this->extraHeaderAttributes(['class' => 'w-1']));

        Request::macro('isFromLivewire', function () {
            return $this->headers->has('x-livewire');
        });

        TextInput::configureUsing(function (TextInput $input) {
            if ($input->isNumeric()) {
                $input->minValue(0);
            }
        });

        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_END,
            fn () => view('filament.sidebar-footer')
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->enforceMorphMap();

        tap($this->app->isLocal(), function (bool $shouldBeEnabled) {
            Model::preventLazyLoading($shouldBeEnabled);
            // Model::preventAccessingMissingAttributes($shouldBeEnabled);
            // in create beneficiary page we use some inputs that doesn't exist in db
            // Model::preventSilentlyDiscardingAttributes($shouldBeEnabled);
        });

        TextEntry::configureUsing(function (TextEntry $entry) {
            return $entry->default('-');
        });
    }

    protected function enforceMorphMap(): void
    {
        Relation::enforceMorphMap([
            'activity' => Activity::class,
            'address' => Address::class,
            'aggressor' => Aggressor::class,
            'beneficiary' => Beneficiary::class,
            'beneficiaryAntecedents' => BeneficiaryAntecedents::class,
            'beneficiaryDetails' => BeneficiaryDetails::class,
            'beneficiaryIntervention' => BeneficiaryIntervention::class,
            'beneficiaryPartner' => BeneficiaryPartner::class,
            'beneficiarySituation' => BeneficiarySituation::class,
            'benefit' => Benefit::class,
            'benefitService' => BenefitService::class,
            'benefit_type' => BenefitType::class,
            'children' => Children::class,
            'city' => City::class,
            'closeFile' => CloseFile::class,
            'community_profile' => CommunityProfile::class,
            'country' => Country::class,
            'county' => County::class,
            'detailedEvaluationResult' => DetailedEvaluationResult::class,
            'detailedEvaluationSpecialist' => DetailedEvaluationSpecialist::class,
            'document' => Document::class,
            'evaluateDetails' => EvaluateDetails::class,
            'flowPresentation' => FlowPresentation::class,
            'institution' => Institution::class,
            'interventionMeeting' => InterventionMeeting::class,
            'interventionPlan' => InterventionPlan::class,
            'interventionPlanResult' => InterventionPlanResult::class,
            'interventionService' => InterventionService::class,
            'meeting' => Meeting::class,
            'monitoring' => Monitoring::class,
            'monitoringChild' => MonitoringChild::class,
            'monthlyPlan' => MonthlyPlan::class,
            'monthlyPlanInterventions' => MonthlyPlanInterventions::class,
            'monthlyPlanService' => MonthlyPlanService::class,
            'multidisciplinaryEvaluation' => MultidisciplinaryEvaluation::class,
            'organization' => Organization::class,
            'organization_service' => OrganizationService::class,
            'organization_service_intervention' => OrganizationServiceIntervention::class,
            'organization_user_permissions' => OrganizationUserPermissions::class,
            'referring_institution' => ReferringInstitution::class,
            'requestedServices' => RequestedServices::class,
            'result' => Result::class,
            'riskFactors' => RiskFactors::class,
            'role' => Role::class,
            'service' => Service::class,
            'service_counseling_sheet' => ServiceCounselingSheet::class,
            'service_intervention' => ServiceIntervention::class,
            'service_pivot' => ServicePivot::class,
            'specialist' => Specialist::class,
            'user' => User::class,
            'user_role' => UserRole::class,
            'user_status' => UserStatus::class,
            'violence' => Violence::class,
            'violenceHistory' => ViolenceHistory::class,
        ]);
    }

    protected function registerBlueprintMacros(): void
    {
        Blueprint::macro('county', function (?string $name = null) {
            $column = collect([$name, 'county_id'])
                ->filter()
                ->join('_');

            return $this->foreignIdFor(County::class, $column)
                ->nullable()
                ->constrained('countries')
                ->cascadeOnDelete();
        });

        Blueprint::macro('city', function (?string $name = null) {
            $column = collect([$name, 'city_id'])
                ->filter()
                ->join('_');

            return $this->foreignIdFor(City::class, $column)
                ->nullable()
                ->constrained('cities')
                ->cascadeOnDelete();
        });
    }

    protected function registerViteMacros(): void
    {
        Vite::macro('image', fn (string $asset) => $this->asset("resources/images/{$asset}"));
    }
}
