@php
    $composeReport();
    $reportData = $getReportData();
    $header = $getHorizontalHeader();
    $headerKey = '';
    $subHeader = $getHorizontalSubHeader();
    $subHeaderKey = $getSubHeaderKey();
    $verticalHeader = $getVerticalHeader();
    $verticalHeaderKey = $getVerticalHeaderKey();
    $verticalSubHeader = $getVerticalSubHeader();
    $verticalSubHeaderKey = $getVerticalSubHeaderKey();
    $firstHeaderElementColSpan = $verticalSubHeader ? 2 : 1;
    $firstHeaderElementRowSpan = $subHeader ? 2 : 1;

    $footer = 'Total cazuri'

@endphp

<div class="w-full overflow-x-auto border border-gray-200 rounded-lg dark:border-white/5">
    <x-filament-tables::table>
        <x-slot:header>
            <x-filament-tables::row>
                @foreach($header as $key => $headerElement)
                    @if($key === 0)
                        <x-filament-tables::header-cell class="!p-2" colspan="{{ $firstHeaderElementColSpan }}" rowspan="{{ $firstHeaderElementRowSpan }}">
                            {{ $headerElement }}
                        </x-filament-tables::header-cell>
                    @elseif($key === 1 && $subHeader)

                        <x-filament-tables::header-cell class="!p-2" colspan="{{ count($subHeader) }}">
                            {{ $headerElement }}
                        </x-filament-tables::header-cell>

                    @else
                        <x-filament-tables::header-cell class="!p-2" rowspan="{{ $firstHeaderElementRowSpan }}">
                            {{ $headerElement }}
                        </x-filament-tables::header-cell>
                    @endif
                @endforeach
            </x-filament-tables::row>

            <x-filament-tables::row>
                @foreach($subHeader as $subheaderElement)
                        <x-filament-tables::header-cell class="!p-2">
                            {{ $subheaderElement }}
                        </x-filament-tables::header-cell>
                @endforeach
            </x-filament-tables::row>

        </x-slot:header>

        @foreach($verticalHeader as $verticalKey => $header)
            @if ($verticalSubHeader)
                @php
                    $firstSubHeaderRow = true
                @endphp
                @foreach($verticalSubHeader as $verticalSubKey => $subheader)
                    <x-filament-tables::row>
                        @if ($firstSubHeaderRow)
                            <x-filament-tables::header-cell class="p-2" rowspan="{{ count($verticalSubHeader) }}">
                                {{ $header }}
                            </x-filament-tables::header-cell>
                        @endif

                            <x-filament-tables::header-cell class="p-2">
                                {{ $subheader }}
                            </x-filament-tables::header-cell>

                        @foreach($subHeader as $key => $subheaderElement)
                                <x-filament-tables::cell class="p-2 whitespace-normal">
                                    {{ $reportData->filter(function ($item) use ($subHeaderKey, $verticalHeaderKey, $verticalSubHeaderKey, $verticalKey, $verticalSubKey, $key) {
                                            $subHeaderField = $key && $item->$subHeaderKey instanceof BackedEnum ?
                                                $item->$subHeaderKey->value :
                                                $item->$subHeaderKey;
                                            $verticalHeaderField = $item->$verticalHeaderKey instanceof BackedEnum ?
                                                $item->$verticalHeaderKey->value :
                                                $item->$verticalHeaderKey;
                                            $verticalSubHeaderField = $item->$verticalSubHeaderKey instanceof BackedEnum ?
                                                $item->$verticalSubHeaderKey->value :
                                                $item->$verticalSubHeaderKey;

                                            return $verticalHeaderField == $verticalKey &&
                                                $verticalSubHeaderField == $verticalSubKey &&
                                                $subHeaderField === $key;
                                        })
                                        ->first()?->total_cases ?? 0
                                    }}
                                </x-filament-tables::cell>
                        @endforeach

                            <x-filament-tables::cell class="p-2 whitespace-normal">
                                {{ $reportData->filter(function ($item) use ($verticalHeaderKey,$verticalKey, $verticalSubHeaderKey, $verticalSubKey){
                                        $verticalHeaderKeyValue = $item->$verticalHeaderKey instanceof BackedEnum ?
                                                $item->$verticalHeaderKey->value :
                                                $item->$verticalHeaderKey;
                                        $verticalSubHeaderKeyValue = $item->$verticalSubHeaderKey instanceof BackedEnum ?
                                                $item->$verticalSubHeaderKey->value :
                                                $item->$verticalSubHeaderKey;
                                        return $verticalHeaderKeyValue == $verticalKey &&
                                        $verticalSubHeaderKeyValue == $verticalSubKey;
                                    })
                                    ->sum('total_cases') }}
                            </x-filament-tables::cell>
                    </x-filament-tables::row>
                    @php
                        $firstSubHeaderRow = false
                    @endphp
                @endforeach
            @elseif ($subHeader)
                <x-filament-tables::row>
                    <x-filament-tables::header-cell class="p-2">
                        {{ $header }}
                    </x-filament-tables::header-cell>
                    @foreach($subHeader as $key => $subheaderElement)
                        <x-filament-tables::cell class="p-2 whitespace-normal">
                            {{ $reportData->filter(function ($item) use ($subHeaderKey, $verticalHeaderKey, $verticalKey, $key) {
                                    $subHeaderField = $item->$subHeaderKey instanceof BackedEnum ?
                                        $item->$subHeaderKey->value :
                                        $item->$subHeaderKey;
                                    $verticalHeaderField = $item->$verticalHeaderKey instanceof BackedEnum ?
                                        $item->$verticalHeaderKey->value :
                                        $item->$verticalHeaderKey;

                                    return $verticalHeaderField == $verticalKey &&
                                        $subHeaderField == $key;
                                })
                                ->first()?->total_cases ?? 0
                            }}
                        </x-filament-tables::cell>
                    @endforeach
                    <x-filament-tables::cell class="p-2 whitespace-normal">
                        {{ $reportData->filter(function ($item) use ($verticalHeaderKey, $verticalKey) {
                                $verticalHeaderField = $item->$verticalHeaderKey instanceof BackedEnum ?
                                        $item->$verticalHeaderKey->value :
                                        $item->$verticalHeaderKey;

                                return $verticalHeaderField == $verticalKey;
                            })
                            ->sum('total_cases')
                        }}
                    </x-filament-tables::cell>
                </x-filament-tables::row>
            @else
                <x-filament-tables::row>
                    <x-filament-tables::header-cell class="p-2">
                        {{ $header }}
                    </x-filament-tables::header-cell>

                    <x-filament-tables::cell class="p-2 whitespace-normal">
                        {{
                            $reportData->filter(fn ($item) => $item->$verticalHeaderKey instanceof BackedEnum ?
                                    $item->$verticalHeaderKey->value == $verticalKey :
                                    $item->$verticalHeaderKey == $verticalKey)
                                ->first()
                                ?->total_cases
                        }}
                    </x-filament-tables::cell>
                </x-filament-tables::row>
            @endif
        @endforeach

        <x-filament-tables::row>
            <x-filament-tables::header-cell class="p-2" colspan="{{ $firstHeaderElementColSpan }}">
                {{ $footer }}
            </x-filament-tables::header-cell>

            @foreach($subHeader as $key => $subheaderElement)
                <x-filament-tables::cell class="p-2 whitespace-normal">
                    {{ $reportData->filter(function($item) use ($subHeaderKey, $key){
                            $subHeaderField = $item->$subHeaderKey instanceof BackedEnum ? $item->$subHeaderKey->value : $item->$subHeaderKey;
                            return $subHeaderField == $key;
                        })->sum('total_cases') }}
                </x-filament-tables::cell>
            @endforeach

            <x-filament-tables::header-cell class="p-2" colspan="{{ $firstHeaderElementColSpan }}">
                {{ $reportData->sum('total_cases') }}
            </x-filament-tables::header-cell>
        </x-filament-tables::row>


    </x-filament-tables::table>
</div>
