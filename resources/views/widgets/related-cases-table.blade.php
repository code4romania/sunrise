<x-filament-widgets::widget class="fi-wi-table">
    @if ($this->table->getRecords()->isNotEmpty())
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_START, scopes: static::class) }}

        {{ $this->table }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
    @endif
</x-filament-widgets::widget>
