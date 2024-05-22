<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use App\Models\City;
use App\Models\County;
use Closure;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;

class Location extends Component
{
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
            TextEntry::make($this->getCountyField())
                ->label($this->getCountyLabel())
                ->placeholder(__('placeholder.county'))
                ->formatStateUsing(fn ($state) => County::find($state)?->name ?? '-'),

            TextEntry::make($this->getCityField())
                ->label($this->getCityLabel())
                ->placeholder(__('placeholder.city'))
                ->formatStateUsing(fn ($state) => City::find($state)?->name ?? '-'),

            TextEntry::make($this->getAddressField())
                ->label($this->getAddressLabel())
                ->placeholder(__('placeholder.address')),

            EnumEntry::make($this->getEnvironmentField())
                ->label($this->getEnvironmentLabel())
                ->placeholder(__('placeholder.residence_environment')),
        ];
    }
}
