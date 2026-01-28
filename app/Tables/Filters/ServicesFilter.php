<?php

declare(strict_types=1);

namespace App\Tables\Filters;

use App\Models\Service;
use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Concerns\HasOptions;
use Filament\Tables\Filters\Concerns\HasRelationship;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ServicesFilter extends BaseFilter
{
    use HasOptions;
    use HasRelationship;

    protected string | Closure | null $attribute = null;

    protected bool | Closure $isSearchable = false;

    public function attribute(string | Closure | null $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getAttribute(): string
    {
        return $this->evaluate($this->attribute) ?? $this->getName();
    }

    public function searchable(bool | Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(fn () => $this->getServices());

        $this->query(function (Builder $query, array $data = []) {
            if (blank($data['values'] ?? null)) {
                return;
            }

            $query->whereHas('services', function (Builder $query) use ($data) {
                $query->whereIn('id', $data['values']);
            })
                ->dd();
        });

        $this->indicateUsing(function (ServicesFilter $filter, array $state): array {
            if (blank($state['values'] ?? null)) {
                return [];
            }

            $label = $this->getServices()
                ->filter(fn ($value, $key) => \in_array($key, $state['values']))
                ->values()
                ->join(', ', ' & ');

            $indicator = $filter->getIndicator();

            if (! $indicator instanceof Indicator) {
                $indicator = Indicator::make("{$indicator}: {$label}");
            }

            return [$indicator];
        });
    }

    public function getFormField(): CheckboxList
    {
        $field = CheckboxList::make('values')
            ->label($this->getLabel())
            ->searchable($this->isSearchable())
            ->options($this->getOptions())
            ->columns([
                'sm' => 2,
                'lg' => 3,
                'xl' => 4,
            ]);

        if (filled($defaultState = $this->getDefaultState())) {
            $field->default($defaultState);
        }

        return $field;
    }

    private function getServices(): Collection
    {
        return Cache::driver('array')
            ->remember(
                MINUTE_IN_SECONDS,
                'all-services-name-id',
                fn () => Service::pluck('name', 'id')
            );
    }
}
