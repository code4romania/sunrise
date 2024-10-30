<?php

declare(strict_types=1);

namespace App\Filters;

use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class DateFilter extends BaseFilter
{
    protected string | Closure | null $attribute = null;

    public function attribute(string | Closure | null $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getAttribute(): string
    {
        return $this->evaluate($this->attribute) ?? $this->getName();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->form(fn () => [
            Fieldset::make($this->getLabel())
                ->columns($this->getColumns())
                ->schema([
                    DatePicker::make('date_from')
                        ->label(__('general.filters.logged_from'))
                        ->placeholder(today()->subYear()->format('Y-m-d')),

                    DatePicker::make('date_until')
                        ->label(__('general.filters.logged_until'))
                        ->placeholder(today()->format('Y-m-d')),
                ]),
        ]);

        $this->query(function (Builder $query, array $state) {
            return $query
                ->when(data_get($state, 'date_from'), function (Builder $query, string $date) {
                    $query->whereDate($this->getAttribute(), '>=', $date);
                })
                ->when(data_get($state, 'date_until'), function (Builder $query, string $date) {
                    $query->whereDate($this->getAttribute(), '<=', $date);
                });
        });

        $this->indicateUsing(function (array $state): array {
            return collect(['date_from', 'date_until'])
                ->mapWithKeys(function (string $filter) use ($state) {
                    $value = data_get($state, $filter);

                    if (! \is_null($value)) {
                        $value = __('general.filters.' . $filter, [
                            'date' => Carbon::parse($value)->format('Y-m-d'),
                        ]);
                    }

                    return [$filter => $value];
                })
                ->filter()
                ->all();
        });
    }
}
