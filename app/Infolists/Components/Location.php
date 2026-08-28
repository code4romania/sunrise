<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use App\Models\City;
use App\Models\County;
use Closure;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Support\Contracts\HasLabel as HasLabelContract;

class Location extends Component
{
    use EntanglesStateWithSingularRelationship;

    protected string $view = 'filament-schemas::components.grid';

    protected string|Closure|null $countyField = null;

    protected string|Closure|null $countyLabel = null;

    protected bool $hasCity = false;

    protected string|Closure|null $cityField = null;

    protected string|Closure|null $cityLabel = null;

    protected bool $hasAddress = false;

    protected string|Closure|null $addressField = null;

    protected string|Closure|null $addressLabel = null;

    protected bool $hasEnvironment = false;

    protected string|Closure|null $environmentField = null;

    protected string|Closure|null $environmentLabel = null;

    protected string|Closure|null $label = null;

    final public function __construct(?string $id)
    {
        $this->id($id);
    }

    public static function make(?string $id = null): static
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

    public function label(string|Closure|null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getCountyField(): string
    {
        return $this->getStatePath() ? 'county_id' : collect([
            $this->getId(),
            'county_id',
        ])
            ->filter()
            ->join('_');
    }

    public function countyLabel(string $label): static
    {
        $this->countyLabel = $label;

        return $this;
    }

    public function getCountyLabel(): string
    {
        return $this->countyLabel ?? __('field.'.collect([
            $this->getId(),
            'county',
        ])
            ->filter()
            ->join('_'));
    }

    public function city(bool|Closure $condition = true): static
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
        return $this->getStatePath() ? 'city_id' : collect([
            $this->getId(),
            'city_id',
        ])
            ->filter()
            ->join('_');
    }

    public function cityLabel(string $label): static
    {
        $this->cityLabel = $label;

        return $this;
    }

    public function getCityLabel(): string
    {
        return $this->cityLabel ?? __('field.'.collect([
            $this->getId(),
            'city',
        ])
            ->filter()
            ->join('_'));
    }

    public function address(bool|Closure $condition = true): static
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
        return $this->getStatePath() ? 'address' : collect([
            $this->getId(),
            'address',
        ])
            ->filter()
            ->join('_');
    }

    public function addressLabel(string $label): static
    {
        $this->addressLabel = $label;

        return $this;
    }

    public function getAddressLabel(): string
    {
        return $this->addressLabel ?? __('field.'.collect([
            $this->getId(),
            'address',
        ])
            ->filter()
            ->join('_'));
    }

    public function environment(bool|Closure $condition = true): static
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
        return $this->getStatePath() ? 'environment' : collect([
            $this->getId(),
            'environment',
        ])
            ->filter()
            ->join('_');
    }

    public function getEnvironmentLabel(): string
    {
        return __('field.'.collect([
            $this->getId(),
            'environment',
        ])
            ->filter()
            ->join('_'));
    }

    public function getDefaultChildComponents(): array
    {
        $relationshipName = $this->getRelationshipName();

        return [
            TextEntry::make($this->getCountyField())
                ->label($this->getCountyLabel())
                ->formatStateUsing(function (mixed $state, $record) use ($relationshipName): string {
                    $name = County::find($state)?->name;
                    if ($name !== null) {
                        return $name;
                    }
                    $address = $record?->{$relationshipName} ?? null;

                    return $address?->county?->name ?? '-';
                }),

            TextEntry::make($this->getCityField())
                ->label($this->getCityLabel())
                ->formatStateUsing(function (mixed $state, $record) use ($relationshipName): string {
                    $name = City::find($state)?->name;
                    if ($name !== null) {
                        return $name;
                    }
                    $address = $record?->{$relationshipName} ?? null;

                    return $address?->city?->name ?? '-';
                }),

            TextEntry::make($this->getAddressField())
                ->label($this->getAddressLabel())
                ->formatStateUsing(function (mixed $state, $record) use ($relationshipName): ?string {
                    if (filled($state)) {
                        return $state;
                    }
                    $address = $record?->{$relationshipName} ?? null;

                    return $address?->address ?? null;
                }),

            TextEntry::make($this->getEnvironmentField())
                ->label($this->getEnvironmentLabel())
                ->visible($this->hasEnvironment())
                ->formatStateUsing(function (mixed $state, $record) use ($relationshipName): ?string {
                    $value = $state;
                    if (blank($value)) {
                        $address = $record?->{$relationshipName} ?? null;
                        $value = $address?->environment ?? null;
                    }

                    return $value instanceof HasLabelContract ? $value->getLabel() : ($value !== null ? (string) $value : null);
                }),
        ];
    }
}
