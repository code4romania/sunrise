<div class="filament-widget col-span-full">
    @if ($visibleWidgets)
        <div>
            <div class="block">
                <nav class="fi-tabs flex max-w-full gap-x-1 overflow-x-auto mb-6" aria-label="Tabs">
                    @foreach ($visibleWidgets as $index => $widget)
                        <span wire:click="selectWidget({{ $index }})"
                              class="fi-tabs-item group flex items-center gap-x-2 rounded-lg px-4 py-2 text-sm font-medium outline-none transition duration-75
                              {{
                                $currentWidget === $index ?
                                    'fi-active fi-tabs-item-active bg-primary-100 dark:bg-primary-800' :
                                    'bg-white hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5'
                              }}
                                hover:text-gray-500 rounded-md px-3 py-2 text-sm font-medium">
                            {{ $this->getWidgetDisplayName($widget) }}
                        </span>
                    @endforeach
                </nav>
            </div>
        </div>

        {!! $this->widgetHTML !!}
    @endif
</div>
