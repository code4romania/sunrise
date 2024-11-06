<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Models\Aggressor;
use App\Models\Beneficiary;
use App\Models\BeneficiaryPartner;
use App\Models\BeneficiarySituation;
use App\Models\City;
use App\Models\CloseFile;
use App\Models\CommunityProfile;
use App\Models\County;
use App\Models\DetailedEvaluationResult;
use App\Models\Document;
use App\Models\EvaluateDetails;
use App\Models\FlowPresentation;
use App\Models\Institution;
use App\Models\Intervention;
use App\Models\Meeting;
use App\Models\Monitoring;
use App\Models\MultidisciplinaryEvaluation;
use App\Models\Organization;
use App\Models\ReferringInstitution;
use App\Models\RequestedServices;
use App\Models\RiskFactors;
use App\Models\Service;
use App\Models\User;
use App\Models\Violence;
use App\Models\ViolenceHistory;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Column;
use Filament\View\PanelsRenderHook;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Request;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->enforceMorphMap();

        tap($this->app->isLocal(), function (bool $shouldBeEnabled) {
            Model::preventLazyLoading($shouldBeEnabled);
            Model::preventAccessingMissingAttributes($shouldBeEnabled);
        });

        TextEntry::configureUsing(function (TextEntry $entry) {
            return $entry->default('-');
        });

        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
            fn () => view('filament.login-form-before')
        );
    }

    protected function enforceMorphMap(): void
    {
        Relation::enforceMorphMap([
            'institution' => Institution::class,
            'beneficiary' => Beneficiary::class,
            'city' => City::class,
            'community_profile' => CommunityProfile::class,
            'county' => County::class,
            'intervention' => Intervention::class,
            'organization' => Organization::class,
            'referring_institution' => ReferringInstitution::class,
            'service' => Service::class,
            'user' => User::class,
            'document' => Document::class,
            'aggressor' => Aggressor::class,
            'beneficiaryPartner' => BeneficiaryPartner::class,
            'meeting' => Meeting::class,
            'multidisciplinaryEvaluation' => MultidisciplinaryEvaluation::class,
            'detailedEvaluationResult' => DetailedEvaluationResult::class,
            'evaluateDetails' => EvaluateDetails::class,
            'violence' => Violence::class,
            'riskFactors' => RiskFactors::class,
            'requestedServices' => RequestedServices::class,
            'beneficiarySituation' => BeneficiarySituation::class,
            'violenceHistory' => ViolenceHistory::class,
            'closeFile' => CloseFile::class,
            'monitoring' => Monitoring::class,
            'flowPresentation' => FlowPresentation::class,
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
