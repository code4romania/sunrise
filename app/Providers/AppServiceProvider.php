<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Beneficiary;
use App\Models\City;
use App\Models\CommunityProfile;
use App\Models\County;
use App\Models\Intervention;
use App\Models\Organization;
use App\Models\ReferringInstitution;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
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
    }

    protected function enforceMorphMap(): void
    {
        Relation::enforceMorphMap([
            'beneficiary' => Beneficiary::class,
            'city' => City::class,
            'community_profile' => CommunityProfile::class,
            'county' => County::class,
            'intervention' => Intervention::class,
            'organization' => Organization::class,
            'referring_institution' => ReferringInstitution::class,
            'service' => Service::class,
            'user' => User::class,
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
