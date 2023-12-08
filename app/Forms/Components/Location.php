<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Enums\ResidenceEnvironment;
use App\Models\City;
use App\Models\County;
use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\CanBeValidated;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Cache;

class Location extends Component implements CanEntangleWithSingularRelationships
{
    use EntanglesStateWithSingularRelationship;
    use CanBeValidated;

    protected string $view = 'filament-forms::components.grid';

    protected string | Closure | null $countyField = null;

    protected string | Closure | null $countyLabel = null;

    protected bool $hasCity = false;

    protected string | Closure | null $cityField = null;

    protected string | Closure | null $cityLabel = null;

    protected bool $hasAddress = false;

    protected string | Closure | null $addressField = null;

    protected string | Closure | null $addressLabel = null;

    protected bool $hasEnvironment = false;

    protected string | Closure | null $environmentField = null;

    protected string | Closure | null $environmentLabel = null;

    final public function __construct(string | null $id)
    {
        $this->id($id);
    }

    public static function make(string | null $id = null): static
    {
        $static = app(static::class, ['id' => $id]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('full');

        $this->columns();
    }

    public function getCountyField(): string
    {
        return collect([
            $this->getId(),
            'county_id',
        ])
            ->filter()
            ->join('_');
    }

    public function getCountyLabel(): string
    {
        return  __('field.' . collect([
            $this->getId(),
            'county',
        ])
            ->filter()
            ->join('_'));
    }

    public function city(bool | Closure $condition = true): static
    {
        $this->hasCity = $condition;

        return $this;
    }

    public function hasCity(): bool
    {
        return (bool) $this->evaluate($this->hasCity);
    }

    public function getCityField(): string
    {
        return collect([
            $this->getId(),
            'city_id',
        ])
            ->filter()
            ->join('_');
    }

    public function getCityLabel(): string
    {
        return  __('field.' . collect([
            $this->getId(),
            'city',
        ])
            ->filter()
            ->join('_'));
    }

    public function address(bool | Closure $condition = true): static
    {
        $this->hasAddress = $condition;

        return $this;
    }

    public function hasAddress(): bool
    {
        return (bool) $this->evaluate($this->hasAddress);
    }

    public function getAddressField(): string
    {
        return collect([
            $this->getId(),
            'address',
        ])
            ->filter()
            ->join('_');
    }

    public function getAddressLabel(): string
    {
        return  __('field.' . collect([
            $this->getId(),
            'address',
        ])
            ->filter()
            ->join('_'));
    }

    public function environment(bool | Closure $condition = true): static
    {
        $this->hasEnvironment = $condition;

        return $this;
    }

    public function hasEnvironment(): bool
    {
        return (bool) $this->evaluate($this->hasEnvironment);
    }

    public function getEnvironmentField(): string
    {
        return collect([
            $this->getId(),
            'environment',
        ])
            ->filter()
            ->join('_');
    }

    public function getEnvironmentLabel(): string
    {
        return  __('field.' . collect([
            $this->getId(),
            'environment',
        ])
            ->filter()
            ->join('_'));
    }

    public function getChildComponents(): array
    {
        return [
            Select::make($this->getCountyField())
                ->label($this->getCountyLabel())
                ->placeholder(__('placeholder.county'))
                ->options(function () {
                    return Cache::driver('array')
                        ->rememberForever(
                            'counties',
                            fn () => County::pluck('name', 'id')
                        );
                })
                ->searchable()
                ->preload()
                ->lazy()
                ->required($this->isRequired())
                ->disabled($this->isDisabled())
                ->afterStateUpdated(function (Set $set) {
                    $set($this->getCityField(), null);
                })
                ->when(! $this->hasCity(), fn (Select $component) => $component->columnSpanFull()),

            Select::make($this->getCityField())
                ->label($this->getCityLabel())
                ->placeholder(__('placeholder.city'))
                ->lazy()
                ->searchable()
                ->required($this->isRequired())
                ->disabled($this->isDisabled())
                ->getSearchResultsUsing(function (string $search, Get $get) {
                    return City::query()
                        ->where('county_id', (int) $get($this->getCountyField()))
                        ->search($search)
                        ->limit(100)
                        ->get()
                        ->pluck('name', 'id');
                })
                ->getOptionLabelUsing(fn ($value) => City::find($value)?->name)
                ->visible($this->hasCity()),

            TextInput::make($this->getAddressField())
                ->label($this->getAddressLabel())
                ->placeholder(__('placeholder.address'))
                ->required($this->isRequired())
                ->disabled($this->isDisabled())
                ->visible($this->hasAddress())
                ->lazy(),

            Select::make($this->getEnvironmentField())
                ->label($this->getEnvironmentLabel())
                ->placeholder(__('placeholder.residence_environment'))
                ->options(ResidenceEnvironment::options())
                ->enum(ResidenceEnvironment::class)
                ->required($this->isRequired())
                ->disabled($this->isDisabled())
                ->visible($this->hasEnvironment())
                ->lazy(),
        ];
    }
}
