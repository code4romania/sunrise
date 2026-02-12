@php

    $actions = array_filter(
        $getActions(),
    );

@endphp

<div
    {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['flex items-center gap-3']) }}>

    <div class="flex-1 flex flex-row gap-2">
        <h3 class=" text-base font-semibold leading-6 fi-section-header-heading text-gray-950 dark:text-white">
            {{ $getState() }}
        </h3>
        @if($getBadge())
        <x-filament::badge
            :tag="'span'"
            :color="$getBadge()->getColor()"
        >{{$getBadge()->getLabel()}}
        </x-filament::badge>
        @endif
    </div>



    @if (count($actions))
        <div
            class="flex items-center gap-3 shrink-0">

            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</div>
