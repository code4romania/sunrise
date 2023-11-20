<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerCarbonMacros();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        tap($this->app->isLocal(), function (bool $shouldBeEnabled) {
            Model::preventLazyLoading($shouldBeEnabled);
            Model::preventAccessingMissingAttributes($shouldBeEnabled);
        });
    }

    protected function registerCarbonMacros(): void
    {
        Carbon::macro('toFormattedDate', function () {
            return $this->translatedFormat(config('forms.components.date_time_picker.display_formats.date'));
        });

        Carbon::macro('toFormattedDateTime', function () {
            return $this->translatedFormat(config('forms.components.date_time_picker.display_formats.date_time'));
        });

        Carbon::macro('toFormattedDateTimeWithSeconds', function () {
            return $this->translatedFormat(config('forms.components.date_time_picker.display_formats.date_time_with_seconds'));
        });

        Carbon::macro('toFormattedTime', function () {
            return $this->translatedFormat(config('forms.components.date_time_picker.display_formats.time'));
        });

        Carbon::macro('toFormattedTimeWithSeconds', function () {
            return $this->translatedFormat(config('forms.components.date_time_picker.display_formats.time_with_seconds'));
        });
    }
}
