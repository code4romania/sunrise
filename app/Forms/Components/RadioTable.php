<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\Concerns;
use Filament\Forms\Components\Concerns\CanBeCloned;
use Filament\Forms\Components\Concerns\CanGenerateUuids;
use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Filament\Forms\Components\Concerns\HasContainerGridLayout;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Concerns\CanBeCollapsed;
use Filament\Support\Concerns\HasReorderAnimationDuration;

class RadioTable extends Field implements \Filament\Schemas\Components\Contracts\HasExtraItemActions
{
    use CanBeCloned;
    use CanBeCollapsed;
    use CanGenerateUuids;
    use CanLimitItemsLength;
    use HasContainerGridLayout;
    use Concerns\HasExtraItemActions;
    use HasReorderAnimationDuration;

//    protected string $view = 'filament-forms::components.grid';
    protected string $view = 'forms.components.radio-table';

    protected array $radioOptions = [];

    protected array $fields = [];

//    final public function __construct(string|null $id)
//    {
//        $this->id($id);
//    }

    public static function make(string|null $name = null): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function getRadioOptions(): array
    {
        return $this->radioOptions;
    }

    public function setRadioOptions(array $radioOptions): self
    {
        $this->radioOptions = $radioOptions;

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function getDefaultChildComponents(): array
    {
        $components = [];
//        foreach ($this->radioOptions as $key => $radioOption) {
//            $components[] = Placeholder::make($key)
//                ->hiddenLabel()
//                ->content($radioOption);
//        }

        foreach ($this->fields as $key => $label) {
            $components[] = Radio::make($key)
                ->label($label)
                ->inline()
                ->options($this->getRadioOptions())
                ->hiddenOptionLabel();
        }

        return $components;
    }
}
