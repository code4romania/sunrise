<x-filament-panels::page>
    
    <div class="flex flex-col gap-y-6">
        @if ($this->getGroupPages())
        <x-filament::tabs>
            @foreach($this->getGroupPages() as $label => $url)
                @php
                    $currentUrl = request()->url();
                    if (request()->isFromLivewire())
                    {
                        $currentUrl = url()->previous();
                    }
                    $active = $currentUrl === $url;
                @endphp
                <a href="{{$url}}">
                    <x-filament::tabs.item
                        :active="$active"
                    >
                        {{ $label }}
                    </x-filament::tabs.item>
                </a>

            @endforeach

        </x-filament::tabs>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE, scopes: $this->getRenderHookScopes()) }}

        {{ $this->infolist }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER, scopes: $this->getRenderHookScopes()) }}
    </div>
</x-filament-panels::page>
