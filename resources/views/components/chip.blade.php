@props([
    'color' => 'primary',
    'disabled' => false,
])

<div
    {{ $attributes
        ->class([
            'px-3 py-1 ring-1',
            'flex items-center justify-start rounded-full',
            'text-xs font-medium text-center',
            'opacity-50' => $disabled,
            'bg-gray-50 text-gray-600 ring-gray-600 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400' => $disabled,
            'bg-custom-50 text-custom-600 ring-custom-600 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400' => !$disabled,
        ])
        ->style([
            \Filament\Support\get_color_css_variables(
                $color,
                shades: [
                    50,
                    400,
                    600,
                ],
                alias: 'badge',
            ) => $color !== 'gray',
        ])
         }}>
    {{ $slot }}
</div>
