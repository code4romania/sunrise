@props([
    'actions' => [],
    'actionsAlignment' => null,
    'breadcrumbs' => [],
    'heading' => null,
    'leadingActions' => [],
    'subheading' => null,
])

<header
    {{
        $attributes->class([
            'fi-header',
            'fi-header-has-breadcrumbs' => $breadcrumbs,
        ])
    }}
>
    <div>
        @if (filled($leadingActions) || $breadcrumbs)
            <div class="fi-header-breadcrumbs-row mb-2 flex flex-wrap items-center gap-2 sm:gap-3">
                @if (filled($leadingActions))
                    <div class="fi-header-leading-actions flex shrink-0 items-center">
                        <x-filament::actions
                            :actions="$leadingActions"
                            alignment="start"
                        />
                    </div>
                @endif

                @if ($breadcrumbs)
                    <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
                @endif
            </div>
        @endif

        @if (filled($heading))
            <h1 class="fi-header-heading">
                {{ $heading }}
            </h1>
        @endif

        @if (filled($subheading))
            <p class="fi-header-subheading">
                {{ $subheading }}
            </p>
        @endif
    </div>

    @php
        $beforeActions = \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE, scopes: $this->getRenderHookScopes());
        $afterActions = \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER, scopes: $this->getRenderHookScopes());
    @endphp

    @if (filled($beforeActions) || $actions || filled($afterActions))
        <div class="fi-header-actions-ctn">
            {{ $beforeActions }}

            @if ($actions)
                <x-filament::actions
                    :actions="$actions"
                    :alignment="$actionsAlignment"
                />
            @endif

            {{ $afterActions }}
        </div>
    @endif
</header>
