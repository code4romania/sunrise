@php
    use Filament\Support\Enums\MaxWidth;
    
    $id = $getId();
    $isContained = $getContainer()->getParentComponent()->isContained();

    $activeTabClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-active',
        'mt-6',
        match ($maxWidth = $getMaxWidth()) {
            MaxWidth::ExtraSmall, 'xs' => 'max-w-xs',
            MaxWidth::Small, 'sm' => 'max-w-sm',
            MaxWidth::Medium, 'md' => 'max-w-md',
            MaxWidth::Large, 'lg' => 'max-w-lg',
            MaxWidth::ExtraLarge, 'xl' => 'max-w-xl',
            MaxWidth::TwoExtraLarge, '2xl' => 'max-w-2xl',
            MaxWidth::ThreeExtraLarge, '3xl' => 'max-w-3xl',
            MaxWidth::FourExtraLarge, '4xl' => 'max-w-4xl',
            MaxWidth::FiveExtraLarge, '5xl' => 'max-w-5xl',
            MaxWidth::SixExtraLarge, '6xl' => 'max-w-6xl',
            MaxWidth::SevenExtraLarge, '7xl' => 'max-w-7xl',
            default => $maxWidth,
        },
    ]);

    $inactiveTabClasses = 'invisible absolute h-0 overflow-hidden p-0';
@endphp

<div
    x-bind:class="tab === @js($id) ? @js($activeTabClasses) : @js($inactiveTabClasses)"
    {{
        $attributes
            ->merge([
                'aria-labelledby' => $id,
                'id' => $id,
                'role' => 'tabpanel',
                'tabindex' => '0',
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-in-tabs-tab outline-none'])
    }}
>
    {{ $getChildComponentContainer() }}
</div>
