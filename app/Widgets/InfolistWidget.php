<?php

declare(strict_types=1);

namespace App\Widgets;

use Filament\Schemas\Schema;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Concerns\InteractsWithHeaderActions;
use Filament\Widgets\Widget;
use Illuminate\Support\Str;

class InfolistWidget extends Widget implements HasInfolists, HasForms, HasActions
{
    use InteractsWithInfolists;
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithHeaderActions;

    protected string $view = 'widgets.infolist-widget';

    protected function getWidgetInfolist()
    {
        return Schema::make()
            ->record($this->record)
            ->name($this->getDisplayName())
            ->components($this->getInfolistSchema());
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

    public function getHeaderActions(): array
    {
        return [];
    }
}
