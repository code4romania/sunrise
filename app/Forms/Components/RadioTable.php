<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\Concerns;
use Filament\Forms\Components\Contracts\HasExtraItemActions;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Concerns\HasReorderAnimationDuration;

class RadioTable extends Field implements HasExtraItemActions
{
    use Concerns\CanBeCloned;
    use Concerns\CanBeCollapsed;
    use Concerns\CanGenerateUuids;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasContainerGridLayout;
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

    public function getChildComponents(): array
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
