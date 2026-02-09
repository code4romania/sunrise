@php
    $steps = $this->getProgressData();
    $firstIncompleteItem = false;
    $heading = $this->getHeading();
@endphp
<x-filament-widgets::widget>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-800">
        {{-- Header: title + close --}}
        <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-4 dark:border-white/10">
            <h2 class="text-lg font-semibold text-[#324049] dark:text-gray-100">
                {{ $heading }}
            </h2>
            @if ($this->canClose())
                <a
                    href="{{ $this->getCloseUrl() }}"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10 dark:hover:text-gray-300"
                    title="{{ __('general.action.close') }}"
                    aria-label="{{ __('general.action.close') }}"
                >
                    <x-filament::icon icon="heroicon-o-x-mark" class="h-5 w-5" />
                </a>
            @endif
        </div>

        {{-- Progress: line + circles + labels (Figma layout) --}}
        <div class="px-6 py-6">
            <div class="relative flex w-full items-start">
                @foreach ($steps as $index => $step)
                    @if ($index > 0)
                        <div
                            class="h-1 flex-1 min-w-4 mt-[14px] rounded-full {{ $steps[$index - 1]['completed'] ? 'bg-[#6010FF]' : 'bg-gray-200 dark:bg-gray-600' }}"
                            aria-hidden="true"
                        ></div>
                    @endif
                    <a
                        href="{{ $step['link'] }}"
                        class="flex flex-1 min-w-0 flex-col items-center gap-3 text-center group"
                        title="{{ $step['label'] }}"
                    >
                        @if ($step['completed'])
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#6010FF] text-white shadow-sm">
                                <x-filament::icon icon="heroicon-m-check" class="h-5 w-5" />
                            </span>
                        @elseif (! $firstIncompleteItem)
                            @php $firstIncompleteItem = true; @endphp
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#6010FF] text-white shadow-sm">
                                <x-filament::icon icon="heroicon-m-arrow-right" class="h-5 w-5" />
                            </span>
                        @else
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 border-gray-200 bg-white dark:border-gray-600 dark:bg-gray-800" aria-hidden="true">
                                <x-filament::icon icon="heroicon-m-circle" class="h-4 w-4 text-gray-300 dark:text-gray-500" />
                            </span>
                        @endif
                        <span class="text-sm font-medium leading-tight break-words {{ $step['completed'] ? 'text-[#6010FF] dark:text-[#a78bfa]' : 'text-gray-500 dark:text-gray-400' }} group-hover:underline">
                            {{ $step['label'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
