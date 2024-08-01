@php

    $actions = array_filter(
        $getActions(),
        fn(\Filament\Infolists\Components\Actions\Action $action): bool => $action->isVisible(),
    );

@endphp

<div
    {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['flex items-center gap-3']) }}>

    <h3 class="flex-1 text-base font-semibold leading-6 fi-section-header-heading text-gray-950 dark:text-white">
        {{ $getState() }}
    </h3>

    @if (count($actions))
        <div
            class="flex items-center gap-3 shrink-0">

            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</div>
