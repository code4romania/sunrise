@props([
    'content' => null,
    'colspan' => 1,
])

<x-filament-tables::row>
    <x-filament-tables::cell class="bg-white" :colspan="$colspan">
        <div class="w-full px-3 py-4">
            <div class="text-sm leading-6 text-gray-950 dark:text-white">
                {{ $content }}
            </div>
        </div>
    </x-filament-tables::cell>
</x-filament-tables::row>
