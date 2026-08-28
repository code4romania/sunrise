@php
    $state = $getState();
    $color = $getColor($state);
    $icon = $getIcon($state);
    $iconColor = $getIconColor($state);

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-in-text-item-icon h-5 w-5 shrink-0',
        match ($iconColor) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-500',
        },
    ]);

    $iconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables($iconColor, shades: [500]) => $iconColor !== 'gray',
    ]);

    $content = $state instanceof BackedEnum ? $state->label() : $state;

    $actions = array_filter(
        $getActions(),
        fn (\Filament\Actions\Action $action): bool => $action->isVisible(),
    );

@endphp

<div
    {{ $attributes->merge($getExtraAttributes(), escape: false)->class([
        'flex items-center gap-3 p-4 -mx-6 -mt-6 rounded-xl',
        //'ring-1 ring-inset',
        'bg-custom-100/50 text-custom-700 ring-custom-700/10',
    ]) }}

    @style([
        \Filament\Support\get_color_css_variables($color, shades: [100, 700]) => !is_null($color),
    ])>

    <x-filament::icon
        :icon="$icon"
        :class="$iconClasses"
        :style="$iconStyles" />

    <div class="flex-1 text-sm">
        {{ $content }}

        @if (count($actions))
            <div
                class="inline-flex items-center gap-3 shrink-0">

                @foreach ($actions as $action)
                    {{ $action }}
                @endforeach
            </div>
        @endif
    </div>
</div>
