@php
    $hasSimpleLink = filled($getLinkUrl ?? null);
    if (!$hasSimpleLink) {
        $actionsList = is_callable($getActions ?? null) ? ($getActions)() : ($getActions ?? []);
        $actions = is_array($actionsList) ? array_values(array_filter($actionsList)) : [];
    } else {
        $actions = [];
    }
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

    @if ($hasSimpleLink)
        <a
            href="{{ $getLinkUrl() }}"
            class="fi-link relative inline-flex items-center justify-center outline-none hover:underline focus:underline focus:outline-none disabled:pointer-events-none disabled:opacity-70 fi-ac-link-action gap-1.5 px-3 py-2 text-sm font-medium text-primary-600 dark:text-primary-400"
        >
            <x-filament::icon icon="heroicon-o-pencil-square" class="fi-link-icon h-5 w-5" />
            <span class="fi-link-label">{{ $getLinkLabel() }}</span>
        </a>
    @elseif (count($actions))
        <div
            class="flex items-center gap-3 shrink-0">

            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</div>
