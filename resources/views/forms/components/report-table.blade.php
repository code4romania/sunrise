@php
    use Filament\Support\Enums\Alignment;
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

    $footer = __('report.labels.total');

@endphp


<div class="w-full overflow-x-auto border border-gray-200 rounded-lg dark:border-white/5">
    <table class="w-full divide-y divide-gray-950/5 dark:divide-white/20">
        <thead>
            <tr>
                @foreach($header as $key => $headerElement)
                    @if($key === 0)
                        <th class="!p-2 text-center"
                            colspan="{{ $firstHeaderElementColSpan }}"
                            rowspan="{{ $firstHeaderElementRowSpan }}"
                        >
                            {{ $headerElement }}
                        </th>
                    @elseif($key === 1 && $subHeader)

                        <th class="!p-2 text-center" colspan="{{ count($subHeader) }}">
                            {{ $headerElement }}
                        </th>

                    @else
                        <th class="!p-2 text-center" rowspan="{{ $firstHeaderElementRowSpan }}">
                            {{ $headerElement }}
                        </th>
                    @endif
                @endforeach
            </tr>

            @if($subHeader)
                <tr>
                    @foreach($subHeader as $subheaderElement)
                        <th class="!p-2 text-center">
                            {{ $subheaderElement }}
                        </th>
                    @endforeach
                </tr>
            @endif

        </thead>
        <tbody>

        @foreach($verticalHeader as $verticalKey => $header)
            @if ($verticalSubHeader)
                @php
                    $firstSubHeaderRow = true
                @endphp
                @foreach($verticalSubHeader as $verticalSubKey => $subheader)
                    <tr>
                        @if ($firstSubHeaderRow)
                            <th class="p-2" rowspan="{{ count($verticalSubHeader) }}">
                                {{ $header }}
                            </th>
                        @endif

                        <th class="p-2 ps-6">
                            {{ $subheader }}
                        </th>

                        @foreach($subHeader as $key => $subheaderElement)
                            <td class="p-2 whitespace-normal text-center">
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
                            </td>
                        @endforeach

                        <td class="p-2 whitespace-normal text-center">
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
                        </td>
                    </tr>
                    @php
                        $firstSubHeaderRow = false
                    @endphp
                @endforeach
            @elseif ($subHeader)
                <tr>
                    <th class="p-2">
                        {{ $header }}
                    </th>
                    @foreach($subHeader as $key => $subheaderElement)
                        <td class="p-2 whitespace-normal text-center">
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
                        </td>
                    @endforeach
                    <td class="p-2 whitespace-normal text-center">
                        {{ $reportData->filter(function ($item) use ($verticalHeaderKey, $verticalKey) {
                                $verticalHeaderField = $item->$verticalHeaderKey instanceof BackedEnum ?
                                        $item->$verticalHeaderKey->value :
                                        $item->$verticalHeaderKey;

                                return $verticalHeaderField == $verticalKey;
                            })
                            ->sum('total_cases')
                        }}
                    </td>
                </tr>
            @else
                <tr>
                    <th class="p-2">
                        {{ $header }}
                    </th>

                    <td class="p-2 whitespace-normal text-center">
                        {{
                            $reportData->filter(fn ($item) => $item->$verticalHeaderKey instanceof BackedEnum ?
                                    $item->$verticalHeaderKey->value == $verticalKey :
                                    $item->$verticalHeaderKey == $verticalKey)
                                ->first()
                                ?->total_cases ?? 0
                        }}
                    </td>
                </tr>
            @endif
        @endforeach

        <tr>
            <th class="p-2" colspan="{{ $firstHeaderElementColSpan }}">
                {{ $footer }}
            </th>

            @foreach($subHeader as $key => $subheaderElement)
                <td class="p-2 whitespace-normal text-center">
                    {{ $reportData->filter(function($item) use ($subHeaderKey, $key){
                            $subHeaderField = $item->$subHeaderKey instanceof BackedEnum ? $item->$subHeaderKey->value : $item->$subHeaderKey;
                            return $subHeaderField == $key;
                        })->sum('total_cases') }}
                </td>
            @endforeach

            <th class="p-2 text-center" colspan="{{ $firstHeaderElementColSpan }}">
                {{ $reportData->sum('total_cases') }}
            </th>
        </tr>
        </tbody>
    </table>
</div>

<style>
    tr {
        text-align: center;
    }
</style>
