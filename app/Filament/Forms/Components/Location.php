<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use App\Models\City;
use App\Models\County;
use Filament\Forms\Components\Concerns\CanBeValidated;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;

class Location extends Grid
{
    use CanBeValidated;

    protected bool $withCity = true;

    public function withoutCity(): self
    {
        $this->withCity = false;

        return $this;
    }

    public function getChildComponents(): array
    {
        return [
            Select::make('county_id')
                ->label(__('field.county'))
                ->options(function () {
                    return Cache::driver('array')
                        ->rememberForever(
                            'counties',
                            fn () => County::pluck('name', 'id')
                        );
                })
                ->searchable()
                ->preload()
                ->reactive()
                ->required($this->isRequired())
                ->afterStateUpdated(fn (callable $set) => $set('city_id', null))
                ->when(! $this->withCity, fn ($component) => $component->columnSpanFull()),

            Select::make('city_id')
                ->label(__('field.city'))
                ->searchable()
                ->required($this->isRequired())
                ->getSearchResultsUsing(function (string $search, callable $get) {
                    $countyId = (int) $get('county_id');

                    if (! $countyId) {
                        return [];
                    }

                    return City::query()
                        ->where('county_id', $countyId)
                        ->search($search)
                        ->limit(100)
                        ->get()
                        ->pluck('name', 'id');
                })
                ->getOptionLabelUsing(fn ($value) => City::find($value)?->name)
                ->visible($this->withCity),
        ];
    }
}
