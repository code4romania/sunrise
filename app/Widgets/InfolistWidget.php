<?php

declare(strict_types=1);

namespace App\Widgets;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Concerns\InteractsWithHeaderActions;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Support\Str;

class InfolistWidget extends Widget implements HasInfolists, HasForms, HasActions
{
    use InteractsWithInfolists;
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithHeaderActions;

    protected string $view = 'widgets.infolist-widget';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->name($this->getDisplayName())
            ->schema($this->getInfolistSchema());
    }

    protected function getWidgetInfolist()
    {
        return $this->cacheSchema('infolist', function () {
            return $this->infolist(Schema::make());
        });
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
