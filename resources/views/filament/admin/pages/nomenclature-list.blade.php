<x-filament-panels::page>



    <div class="flex flex-col gap-y-6">
        <x-filament::tabs>
            <a href="{{route('filament.admin.resources.services.index')}}">
                <x-filament::tabs.item
                    :active="request()->routeIs('filament.admin.resources.services.index')"
                >
                    {{ __('nomenclature.headings.service') }}
                </x-filament::tabs.item>
            </a>

            <a href="{{route('filament.admin.resources.benefits.index')}}">
                <x-filament::tabs.item
                    :active="request()->routeIs('filament.admin.resources.benefits.index')"
                >
                    {{ __('nomenclature.headings.benefits') }}
                </x-filament::tabs.item>
            </a>

            <a href="{{route('filament.admin.resources.roles.index')}}">
                <x-filament::tabs.item
                    :active="request()->routeIs('filament.admin.resources.roles.index')"
                >
                    {{ __('nomenclature.headings.roles') }}
                </x-filament::tabs.item>
            </a>


        </x-filament::tabs>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE, scopes: $this->getRenderHookScopes()) }}

        {{ $this->table }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER, scopes: $this->getRenderHookScopes()) }}
    </div>
</x-filament-panels::page>
