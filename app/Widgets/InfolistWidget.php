<?php

declare(strict_types=1);

namespace App\Widgets;

use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Widgets\Widget;
use Illuminate\Support\Str;

class InfolistWidget extends Widget implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $view = 'widgets.infolist-widget';

    protected function getWidgetInfolist()
    {
        return Infolist::make()
            ->record($this->record)
            ->name($this->getDisplayName())
            ->schema($this->getInfolistSchema());
    }

    protected function getInfolistSchema(): array
    {
        return [];
    }

    public function getDisplayName(): string
    {
        return Str::of($this::class)
            ->afterLast('\\')
            ->kebab()
            ->replace('-', ' ')
            ->title()
            ->toString();
    }
}
