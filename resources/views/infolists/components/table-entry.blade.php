@php
    use Filament\Forms\Components\Actions\Action;

    $containers = $getChildComponentContainers();

    $headers = $getHeaders();
    $columnWidths = $getColumnWidths();
    $breakPoint = $getBreakPoint();
    $hasContainers = count($containers) > 0;
    $hasHiddenHeader = $shouldHideHeader();
    $statePath = $getStatePath();

    $emptyLabel = $getEmptyLabel();

@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry" xmlns:x-filament="http://www.w3.org/1999/html">
    <div
        x-data="{}"
        {{ $attributes->merge($getExtraAttributes())->class([
            'filament-table-repeater-component space-y-6 relative',
            match ($breakPoint) {
                'sm' => 'break-point-sm',
                'lg' => 'break-point-lg',
                'xl' => 'break-point-xl',
                '2xl' => 'break-point-2xl',
                default => 'break-point-md',
            }
        ]) }}
    >
        @if (count($containers) || $emptyLabel !== false)
            <div @class([
                'filament-table-repeater-container rounded-xl relative ring-1 ring-gray-950/5 dark:ring-white/20',
                'sm:ring-gray-950/5 dark:sm:ring-white/20' => ! $hasContainers && $breakPoint === 'sm',
                'md:ring-gray-950/5 dark:md:ring-white/20' => ! $hasContainers && $breakPoint === 'md',
                'lg:ring-gray-950/5 dark:lg:ring-white/20' => ! $hasContainers && $breakPoint === 'lg',
                'xl:ring-gray-950/5 dark:xl:ring-white/20' => ! $hasContainers && $breakPoint === 'xl',
                '2xl:ring-gray-950/5 dark:2xl:ring-white/20' => ! $hasContainers && $breakPoint === '2xl',
            ])>
                <x-filament-tables::table class="w-full">
                    <x-slot:header @class([
                        'filament-table-repeater-header-hidden sr-only' => $hasHiddenHeader,
                        'filament-table-repeater-header rounded-t-xl overflow-hidden border-b border-gray-950/5 dark:border-white/20' => ! $hasHiddenHeader,
                    ])>
                        <x-filament-tables::row class="text-xs md:divide-x md:divide-gray-950/5 dark:md:divide-white/20">
                            @foreach ($headers as $key => $header)
                                <x-filament-tables::header-cell
                                    @class([
                                        'filament-table-repeater-header-column px-3 py-2 font-medium  bg-gray-100 dark:text-gray-300 dark:bg-gray-900/60',
                                        'ltr:rounded-tl-xl rtl:rounded-tr-xl' => $loop->first,
                                        'ltr:rounded-tr-xl rtl:rounded-tl-xl' => $loop->last,
                                        match($getHeadersAlignment()) {
                                          'center' => 'text-center',
                                          'right' => 'text-right rtl:text-left',
                                          default => 'text-left rtl:text-right'
                                        }
                                    ])
                                    @style([
                                        'width: ' . $header['width'] => $header['width'],
                                    ])
                                >
                                    {{ $header['label'] }}
                                </x-filament-tables::header-cell>
                            @endforeach
                        </x-filament-tables::row>
                    </x-slot:header>
                    <tbody
                        x-sortable
                        wire:end.stop="{{ 'mountFormComponentAction(\'' . $statePath . '\', \'reorder\', { items: $event.target.sortable.toArray() })' }}"
                        class="filament-table-repeater-rows-wrapper divide-y divide-gray-950/5 dark:divide-white/20"
                    >
                        @if (count($containers))
                            @foreach ($containers as $uuid => $row)
                                <x-filament-tables::row class="filament-table-repeater-row md:divide-x md:divide-gray-950/5 dark:md:divide-white/20">
                                    @foreach($row->getComponents() as $cell)
                                        @if(! $cell instanceof \Filament\Forms\Components\Hidden && ! $cell->isHidden())
                                            @php
                                                $cellKey = method_exists($cell, 'getName') ? $cell->getName() : $cell->getId();
//                                                if (!$cell->getState())
//                                                debug($cell->getState(), $cell->getStatePath());
                                            @endphp
                                            <td
                                                class="filament-table-repeater-column p-2"
                                                @if ($columnWidths && isset($columnWidths[$cellKey]))
                                                    style="width: {{$columnWidths[$cellKey]}}"
                                                @endif
                                            >
                                                {{ $cell }}
                                            </td>
                                        @else
                                            {{ $cell }}
                                        @endif
                                    @endforeach

                                </x-filament-tables::row>
                            @endforeach
                        @else
                            <x-filament-tables::row class="filament-table-repeater-row filament-table-repeater-empty-row md:divide-x md:divide-gray-950/5 dark:md:divide-divide-white/20">
                                <x-filament-tables::cell colspan="{{ count($headers) }}" class="filament-table-repeater-column filament-table-repeater-empty-column p-4 w-px text-center italic">
                                    {{ $emptyLabel ?: __('filament-table-repeater::components.repeater.empty.label') }}
                                </x-filament-tables::cell>
                            </x-filament-tables::row>
                        @endif
                    </tbody>
                </x-filament-tables::table>
            </div>
        @endif
    </div>
</x-dynamic-component>
