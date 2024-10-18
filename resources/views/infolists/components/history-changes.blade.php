@php
    $fields = $getFields();
    $state = $getState();
@endphp

<div class="w-full overflow-x-auto border border-gray-200 rounded-lg dark:border-white/5">
    <x-filament-tables::table>
        <x-slot:header>
            <x-filament-tables::header-cell class="!p-2">
                {{ __('general.field') }}
            </x-filament-tables::header-cell>

            <x-filament-tables::header-cell class="!p-2">
                {{ __('general.old') }}
            </x-filament-tables::header-cell>

            <x-filament-tables::header-cell class="!p-2">
                {{ __('general.new') }}
            </x-filament-tables::header-cell>
        </x-slot:header>

        @foreach ($fields as $field)
            @php
                $oldValue = data_get($state, "old.{$field}");
                $newValue = data_get($state, "attributes.{$field}");

                if (blank($oldValue) && blank($newValue))
                {
                    continue;
                }

                $lineData = $getLineData($field, $oldValue, $newValue);

                $oldValue = $lineData->get('old');
                $newValue = $lineData->get('new');

            @endphp

            @if($lineData->get('subFields'))

                @foreach($lineData->get('subFields') as $itemKey)
                    @php
                        $subLineData = $getLineData($itemKey, $oldValue->get($itemKey), $newValue->get($itemKey));
                        $oldSubLineValue = $subLineData->get('old');
                        $newSubLineValue = $subLineData->get('new');

                        if (!$subLineData->get('subfields') &&
                            (
                                (blank($oldSubLineValue) && blank($newSubLineValue)) ||
                                $oldSubLineValue === $newSubLineValue
                            )
                        )
                        {
                            continue;
                        }

                    @endphp

                    @if($subLineData->get('subFields'))
                        @php $headerIsDisplayed = false @endphp
                        @foreach($subLineData->get('subFields') as $subField)
                            @php
                                $subSubLineData = $getLineData($subField, $oldSubLineValue->get($subField), $newSubLineValue->get($subField));
                                if (
                                    (blank($subSubLineData->get('old')) && blank($subSubLineData->get('new'))) ||
                                    $subSubLineData->get('old') === $subSubLineData->get('new')
                                )
                                {
                                    continue;
                                }
                            @endphp

                            @if(!$headerIsDisplayed)
                                <x-filament-tables::row>
                                    <x-filament-tables::header-cell class="p-2">
                                        {{ $lineData->get('label') }}
                                    </x-filament-tables::header-cell>
                                </x-filament-tables::row>

                                @php $headerIsDisplayed = true; @endphp
                            @endif

                            <x-history-line :fieldLabel="$subSubLineData->get('label')"
                                            :oldValue="$subSubLineData->get('old')"
                                            :newValue="$subSubLineData->get('new')">
                            </x-history-line>
                        @endforeach
                    @else

                        <x-history-line :fieldLabel="$subLineData->get('label')"
                                        :oldValue="$oldSubLineValue"
                                        :newValue="$newSubLineValue">
                        </x-history-line>

                    @endif

                @endforeach
            @else
                <x-history-line :fieldLabel="$lineData->get('label')"
                                :oldValue="$oldValue"
                                :newValue="$newValue">
                </x-history-line>
            @endif
        @endforeach
    </x-filament-tables::table>
</div>
