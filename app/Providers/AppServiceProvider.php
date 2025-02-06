<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
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

        $this->app->bind(LoginResponseContract::class, LoginResponse::class);

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
            'activity' => \App\Models\Activity::class,
            'address' => \App\Models\Address::class,
            'aggressor' => \App\Models\Aggressor::class,
            'beneficiary' => \App\Models\Beneficiary::class,
            'beneficiaryAntecedents' => \App\Models\BeneficiaryAntecedents::class,
            'beneficiaryDetails' => \App\Models\BeneficiaryDetails::class,
            'beneficiaryIntervention' => \App\Models\BeneficiaryIntervention::class,
            'beneficiaryPartner' => \App\Models\BeneficiaryPartner::class,
            'beneficiarySituation' => \App\Models\BeneficiarySituation::class,
            'benefit' => \App\Models\Benefit::class,
            'benefitService' => \App\Models\BenefitService::class,
            'benefit_type' => \App\Models\BenefitType::class,
            'children' => \App\Models\Children::class,
            'city' => \App\Models\City::class,
            'closeFile' => \App\Models\CloseFile::class,
            'community_profile' => \App\Models\CommunityProfile::class,
            'country' => \App\Models\Country::class,
            'county' => \App\Models\County::class,
            'detailedEvaluationResult' => \App\Models\DetailedEvaluationResult::class,
            'detailedEvaluationSpecialist' => \App\Models\DetailedEvaluationSpecialist::class,
            'document' => \App\Models\Document::class,
            'evaluateDetails' => \App\Models\EvaluateDetails::class,
            'flowPresentation' => \App\Models\FlowPresentation::class,
            'institution' => \App\Models\Institution::class,
            'interventionMeeting' => \App\Models\InterventionMeeting::class,
            'interventionPlan' => \App\Models\InterventionPlan::class,
            'interventionPlanResult' => \App\Models\InterventionPlanResult::class,
            'interventionService' => \App\Models\InterventionService::class,
            'meeting' => \App\Models\Meeting::class,
            'monitoring' => \App\Models\Monitoring::class,
            'monitoringChild' => \App\Models\MonitoringChild::class,
            'monthlyPlan' => \App\Models\MonthlyPlan::class,
            'monthlyPlanInterventions' => \App\Models\MonthlyPlanInterventions::class,
            'monthlyPlanService' => \App\Models\MonthlyPlanService::class,
            'multidisciplinaryEvaluation' => \App\Models\MultidisciplinaryEvaluation::class,
            'organization' => \App\Models\Organization::class,
            'organization_service' => \App\Models\OrganizationService::class,
            'organization_service_intervention' => \App\Models\OrganizationServiceIntervention::class,
            'organization_user_permissions' => \App\Models\OrganizationUserPermissions::class,
            'referring_institution' => \App\Models\ReferringInstitution::class,
            'requestedServices' => \App\Models\RequestedServices::class,
            'result' => \App\Models\Result::class,
            'riskFactors' => \App\Models\RiskFactors::class,
            'role' => \App\Models\Role::class,
            'service' => \App\Models\Service::class,
            'service_counseling_sheet' => \App\Models\ServiceCounselingSheet::class,
            'service_intervention' => \App\Models\ServiceIntervention::class,
            'service_pivot' => \App\Models\ServicePivot::class,
            'specialist' => \App\Models\Specialist::class,
            'user' => \App\Models\User::class,
            'user_role' => \App\Models\UserRole::class,
            'user_status' => \App\Models\UserStatus::class,
            'violence' => \App\Models\Violence::class,
            'violenceHistory' => \App\Models\ViolenceHistory::class,
        ]);
    }

    protected function registerBlueprintMacros(): void
    {
        Blueprint::macro('county', function (?string $name = null) {
            $column = collect([$name, 'county_id'])
                ->filter()
                ->join('_');

            return $this->foreignIdFor(\App\Models\County::class, $column)
                ->nullable()
                ->constrained('countries')
                ->cascadeOnDelete();
        });

        Blueprint::macro('city', function (?string $name = null) {
            $column = collect([$name, 'city_id'])
                ->filter()
                ->join('_');

            return $this->foreignIdFor(\App\Models\City::class, $column)
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
