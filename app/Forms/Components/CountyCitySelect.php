<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Models\City;
use App\Models\County;
use Closure;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Contracts\Support\Htmlable;

class CountyCitySelect
{
    protected string $countyField = 'county_id';

    protected string $cityField = 'city_id';

    protected string|Closure|Htmlable|null $countyLabel = null;

    protected string|Closure|Htmlable|null $cityLabel = null;

    protected string|Closure|null $countyPlaceholder = null;

    protected string|Closure|null $cityPlaceholder = null;

    protected bool $required = true;

    protected bool $useRelationship = false;

    /** @var Closure(Set, Get): void|null */
    protected ?Closure $countyAfterStateUpdated = null;

    /** @var Closure(Set, Get, mixed): void|null */
    protected ?Closure $cityAfterStateUpdated = null;

    /** @var Closure(Get): bool|null */
    protected ?Closure $countyDisabled = null;

    /** @var Closure(Get): bool|null */
    protected ?Closure $cityDisabled = null;

    public static function make(): static
    {
        return new static;
    }

    public function countyField(string $field): static
    {
        $this->countyField = $field;

        return $this;
    }

    public function cityField(string $field): static
    {
        $this->cityField = $field;

        return $this;
    }

    public function countyLabel(string|Closure|Htmlable $label): static
    {
        $this->countyLabel = $label;

        return $this;
    }

    public function cityLabel(string|Closure|Htmlable $label): static
    {
        $this->cityLabel = $label;

        return $this;
    }

    public function countyPlaceholder(string|Closure|null $placeholder): static
    {
        $this->countyPlaceholder = $placeholder;

        return $this;
    }

    public function cityPlaceholder(string|Closure|null $placeholder): static
    {
        $this->cityPlaceholder = $placeholder;

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function useRelationship(bool $use = true): static
    {
        $this->useRelationship = $use;

        return $this;
    }

    /**
     * @param Closure(Set, Get): void $callback
     */
    public function countyAfterStateUpdated(Closure $callback): static
    {
        $this->countyAfterStateUpdated = $callback;

        return $this;
    }

    /**
     * @param Closure(Set, Get, mixed): void $callback
     */
    public function cityAfterStateUpdated(Closure $callback): static
    {
        $this->cityAfterStateUpdated = $callback;

        return $this;
    }

    /**
     * @param Closure(Get): bool $callback
     */
    public function countyDisabled(Closure $callback): static
    {
        $this->countyDisabled = $callback;

        return $this;
    }

    /**
     * @param Closure(Get): bool $callback
     */
    public function cityDisabled(Closure $callback): static
    {
        $this->cityDisabled = $callback;

        return $this;
    }

    /**
     * @return array<int, Select>
     */
    public function schema(): array
    {
        $countyField = $this->countyField;
        $cityField = $this->cityField;
        $countyPlaceholder = $this->countyPlaceholder ?? __('placeholder.county');
        $cityPlaceholder = $this->cityPlaceholder ?? __('placeholder.city');
        $countyAfterStateUpdated = $this->countyAfterStateUpdated;

        $countySelect = Select::make($this->countyField)
            ->label($this->countyLabel)
            ->placeholder($countyPlaceholder)
            ->searchable()
            ->preload()
            ->getSearchResultsUsing(fn (string $search) => County::search($search)
                ->get()
                ->pluck('name', 'id')
                ->toArray())
            ->live()
            ->afterStateUpdated(function (Set $set, Get $get) use ($cityField, $countyAfterStateUpdated): void {
                $set($cityField, null);
                if ($countyAfterStateUpdated instanceof Closure) {
                    $countyAfterStateUpdated($set, $get);
                }
            });

        if ($this->useRelationship && $this->countyField === 'county_id') {
            $countySelect->relationship('county', 'name');
        } else {
            $countySelect->options(
                County::query()
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray()
            );
        }

        if ($this->countyDisabled instanceof Closure) {
            $countySelect->disabled($this->countyDisabled);
        }

        if ($this->required) {
            $countySelect->required();
        }

        $cityDisabled = $this->cityDisabled;
        $cityAfterStateUpdated = $this->cityAfterStateUpdated;

        $citySelect = Select::make($this->cityField)
            ->label($this->cityLabel)
            ->placeholder($cityPlaceholder)
            ->options(function (Get $get) use ($countyField): array {
                $countyId = $get($countyField);
                if (! $countyId) {
                    return [];
                }

                return City::query()
                    ->where('county_id', $countyId)
                    ->get()
                    ->mapWithKeys(fn (City $city) => [$city->id => $city->name_with_uat ?? $city->name])
                    ->toArray();
            })
            ->searchable()
            ->getSearchResultsUsing(
                fn (string $search, Get $get) => City::search($search)
                    ->where('county_id', $get($countyField))
                    ->get()
                    ->pluck('name', 'id')
            )
            ->disabled(
                $cityDisabled instanceof Closure
                    ? $cityDisabled
                    : fn (Get $get) => ! $get($countyField)
            );

        if ($cityAfterStateUpdated instanceof Closure) {
            $citySelect->live()->afterStateUpdated($cityAfterStateUpdated);
        }

        if ($this->required) {
            $citySelect->required();
        }

        return [$countySelect, $citySelect];
    }

    /**
     * Spread into a schema array: ...$component->schema().
     *
     * @return array<int, Select>
     */
    public function __invoke(): array
    {
        return $this->schema();
    }
}
