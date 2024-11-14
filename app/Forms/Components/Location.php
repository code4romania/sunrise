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
use Filament\Forms\Components\Hidden;
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

    protected string | null $countyLabel = null;

    protected bool $hasCity = false;

    protected string | Closure | null $cityField = null;

    protected string | null $cityLabel = null;

    protected bool $hasAddress = false;

    protected string | Closure | null $addressField = null;

    protected string | null $addressLabel = null;

    protected int | null $addressMaxLength = 255;

    protected bool $hasEnvironment = false;

    protected string | Closure | null $environmentField = null;

    protected string | Closure | null $environmentLabel = null;

    protected Closure|string|null $copyPath = null;

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
        return $this->getRelationshipName() ? 'county_id' : collect([
            $this->getId(),
            'county_id',
        ])
            ->filter()
            ->join('_');
    }

    public function getCountyLabel(): string
    {
        return $this->countyLabel ?? __('field.' . collect([
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
        return $this->getRelationshipName() ? 'city_id' : collect([
            $this->getId(),
            'city_id',
        ])
            ->filter()
            ->join('_');
    }

    public function getCityLabel(): string
    {
        return  $this->cityLabel ?? __('field.' . collect([
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
        return $this->getRelationshipName() ? 'address' : collect([
            $this->getId(),
            'address',
        ])
            ->filter()
            ->join('_');
    }

    public function getAddressLabel(): string
    {
        return $this->addressLabel ?? __('field.' . collect([
            $this->getId(),
            'address',
        ])
            ->filter()
            ->join('_'));
    }

    public function addressMaxLength(?int $addressMaxLength): self
    {
        $this->addressMaxLength = $addressMaxLength;

        return $this;
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
        return $this->getRelationshipName() ? 'environment' : collect([
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
                ->live()
                ->required($this->isRequired())
                ->disabled($this->isDisabled())
                ->afterStateUpdated(function (Set $set, $state) {
                    $set($this->getCityField(), null);
                    if ($this->getCopyPath()) {
                        $set(\sprintf('../%s.county_id', $this->getCopyPath()), $state);
                    }
                })
                ->when(! $this->hasCity(), fn (Select $component) => $component->columnSpanFull()),

            Select::make($this->getCityField())
                ->label($this->getCityLabel())
                ->placeholder(__('placeholder.city'))
                ->live()
                ->searchable()
                ->required($this->isRequired())
                ->disabled(fn (Get $get) => $this->isDisabled() || ! $get($this->getCountyField()))
                ->getSearchResultsUsing(function (string $search, Get $get) {
                    return City::query()
                        ->where('county_id', (int) $get($this->getCountyField()))
                        ->search($search)
                        ->limit(100)
                        ->get()
                        ->pluck('name_with_uat', 'id');
                })
                ->getOptionLabelUsing(fn ($value) => City::find($value)?->name)
                ->visible(fn () => $this->hasCity())
                ->afterStateUpdated(
                    fn (Set $set, $state) => $this->getCopyPath() ?
                        $set(\sprintf('../%s.city_id', $this->getCopyPath()), $state) : null
                ),

            TextInput::make($this->getAddressField())
                ->label($this->getAddressLabel())
                ->placeholder(__('placeholder.address'))
                ->required($this->isRequired())
                ->disabled($this->isDisabled())
                ->visible($this->hasAddress())
                ->maxLength($this->addressMaxLength)
                ->lazy()
                ->afterStateUpdated(
                    fn (Set $set, $state) => $this->getCopyPath() ?
                        $set(\sprintf('../%s.address', $this->getCopyPath()), $state) : null
                ),

            Select::make($this->getEnvironmentField())
                ->label($this->getEnvironmentLabel())
                ->placeholder(__('placeholder.residence_environment'))
                ->options(ResidenceEnvironment::options())
                ->enum(ResidenceEnvironment::class)
                ->required($this->isRequired())
                ->disabled($this->isDisabled())
                ->visible($this->hasEnvironment())
                ->lazy()
                ->afterStateUpdated(
                    fn (Set $set, $state) => $this->getCopyPath() ?
                        $set(\sprintf('../%s.environment', $this->getCopyPath()), $state) : null
                ),

            Hidden::make('address_type')
                ->default($this->getRelationshipName()),
        ];
    }

    public function countyLabel(string $label): static
    {
        $this->countyLabel = $label;

        return $this;
    }

    public function cityLabel(string $label): static
    {
        $this->cityLabel = $label;

        return $this;
    }

    public function addressLabel(string $label): static
    {
        $this->addressLabel = $label;

        return $this;
    }

    public function copyDataInPath(Closure | string | null $path = null): static
    {
        $this->copyPath = $path;

        return $this;
    }

    public function getCopyPath()
    {
        return $this->evaluate($this->copyPath);
    }
}
