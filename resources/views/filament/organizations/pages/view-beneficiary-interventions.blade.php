<x-filament-panels::page>



    <div class="flex flex-col gap-y-6">
        @if ($this->getGroupPages())
        <x-filament::tabs>
            @foreach($this->getGroupPages() as $label => $url)
                <a href="{{$url}}">
                    <x-filament::tabs.item
                        :active="request()->fullUrlIs($url)"
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
