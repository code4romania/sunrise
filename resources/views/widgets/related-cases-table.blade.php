@if ($this->table->getRecords()->isNotEmpty())
    <x-filament-widgets::widget class="fi-wi-table">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_START, scopes: static::class) }}

        {{ $this->table }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
    </x-filament-widgets::widget>
@endif
<div>
    {{-- Required empty for Livewire --}}
</div>
