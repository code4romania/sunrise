@php
    $fields = $getFields();
    $state = $getState();
@endphp

<div class="w-full overflow-x-auto border border-gray-200 rounded-lg dark:border-white/5">
    <x-filament-tables::table>
        <x-slot:header>
            <x-filament-tables::header-cell class="!p-2">
                field
            </x-filament-tables::header-cell>

            <x-filament-tables::header-cell class="!p-2">
                old
            </x-filament-tables::header-cell>

            <x-filament-tables::header-cell class="!p-2">
                new
            </x-filament-tables::header-cell>
        </x-slot:header>

        @foreach ($fields as $field)
            <x-filament-tables::row>
                <x-filament-tables::cell class="p-2">
                    {{ $getFieldLabel($field) }}
                </x-filament-tables::cell>

                <x-filament-tables::cell class="p-2">
                    {{-- {{ $getFieldValue($field, data_get($state, "old.{$field}")) }} --}}
                    {{ data_get($state, "old.{$field}") }}
                </x-filament-tables::cell>

                <x-filament-tables::cell class="p-2">
                    {{-- {{ $getFieldValue($field, data_get($state, "attribute.{$field}")) }} --}}
                    {{ data_get($state, "attributes.{$field}") }}
                </x-filament-tables::cell>
            </x-filament-tables::row>
        @endforeach
    </x-filament-tables::table>
</div>
