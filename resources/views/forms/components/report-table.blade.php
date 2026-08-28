@php
    $table = $getTablePayload();

    $reportData = $table['reportData'];
    $header = $table['header'];
    $subHeader = $table['subHeader'];
    $subHeaderKey = $table['subHeaderKey'];
    $verticalHeader = $table['verticalHeader'];
    $verticalHeaderKey = $table['verticalHeaderKey'];
    $verticalSubHeader = $table['verticalSubHeader'];
    $verticalSubHeaderKey = $table['verticalSubHeaderKey'];
    $firstHeaderElementColSpan = $table['firstHeaderElementColSpan'];
    $firstHeaderElementRowSpan = $table['firstHeaderElementRowSpan'];
@endphp

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                @foreach($header as $key => $headerElement)
                    @if($key === 0)
                        <th colspan="{{ $firstHeaderElementColSpan }}" rowspan="{{ $firstHeaderElementRowSpan }}" class="px-3 py-2 text-left font-semibold text-gray-700">
                            {{ $headerElement }}
                        </th>
                    @elseif($key === 1 && $subHeader)
                        <th colspan="{{ count($subHeader) }}" class="px-3 py-2 text-left font-semibold text-gray-700">
                            {{ $headerElement }}
                        </th>
                    @else
                        <th rowspan="{{ $firstHeaderElementRowSpan }}" class="px-3 py-2 text-left font-semibold text-gray-700">
                            {{ $headerElement }}
                        </th>
                    @endif
                @endforeach
            </tr>

            @if($subHeader)
                <tr>
                    @foreach($subHeader as $subheaderElement)
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">
                            {{ $subheaderElement }}
                        </th>
                    @endforeach
                </tr>
            @endif
        </thead>

        <tbody class="divide-y divide-gray-100">
            @foreach($verticalHeader as $verticalKey => $rowHeader)
                @if ($verticalSubHeader)
                    @php
                        $firstSubHeaderRow = true;
                    @endphp
                    @foreach($verticalSubHeader as $verticalSubKey => $subheader)
                        <tr>
                            @if ($firstSubHeaderRow)
                                <td rowspan="{{ count($verticalSubHeader) }}" class="px-3 py-2 align-top font-medium text-gray-900">
                                    {{ $rowHeader }}
                                </td>
                            @endif

                            <td class="px-3 py-2 text-gray-700">{{ $subheader }}</td>

                            @foreach($subHeader as $key => $subheaderElement)
                                <td class="px-3 py-2 text-gray-700">
                                    {{ $reportData->filter(function ($item) use ($subHeaderKey, $verticalHeaderKey, $verticalSubHeaderKey, $verticalKey, $verticalSubKey, $key) {
                                            $subHeaderField = $item->$subHeaderKey instanceof \BackedEnum ? $item->$subHeaderKey->value : $item->$subHeaderKey;
                                            $verticalHeaderField = $item->$verticalHeaderKey instanceof \BackedEnum ? $item->$verticalHeaderKey->value : $item->$verticalHeaderKey;
                                            $verticalSubHeaderField = $item->$verticalSubHeaderKey instanceof \BackedEnum ? $item->$verticalSubHeaderKey->value : $item->$verticalSubHeaderKey;

                                            return $verticalHeaderField == $verticalKey &&
                                                $verticalSubHeaderField == $verticalSubKey &&
                                                $subHeaderField === $key;
                                        })
                                        ->first()?->total_cases ?? 0
                                    }}
                                </td>
                            @endforeach

                            <td class="px-3 py-2 font-medium text-gray-900">
                                {{ $reportData->filter(function ($item) use ($verticalHeaderKey, $verticalKey, $verticalSubHeaderKey, $verticalSubKey) {
                                        $verticalHeaderValue = $item->$verticalHeaderKey instanceof \BackedEnum ? $item->$verticalHeaderKey->value : $item->$verticalHeaderKey;
                                        $verticalSubHeaderValue = $item->$verticalSubHeaderKey instanceof \BackedEnum ? $item->$verticalSubHeaderKey->value : $item->$verticalSubHeaderKey;

                                        return $verticalHeaderValue == $verticalKey && $verticalSubHeaderValue == $verticalSubKey;
                                    })->sum('total_cases')
                                }}
                            </td>
                        </tr>
                        @php
                            $firstSubHeaderRow = false;
                        @endphp
                    @endforeach
                @elseif ($subHeader)
                    <tr>
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $rowHeader }}</td>
                        @foreach($subHeader as $key => $subheaderElement)
                            <td class="px-3 py-2 text-gray-700">
                                {{ $reportData->filter(function ($item) use ($subHeaderKey, $verticalHeaderKey, $verticalKey, $key) {
                                        $subHeaderField = $item->$subHeaderKey instanceof \BackedEnum ? $item->$subHeaderKey->value : $item->$subHeaderKey;
                                        $verticalHeaderField = $item->$verticalHeaderKey instanceof \BackedEnum ? $item->$verticalHeaderKey->value : $item->$verticalHeaderKey;

                                        return $verticalHeaderField == $verticalKey && $subHeaderField == $key;
                                    })
                                    ->first()?->total_cases ?? 0
                                }}
                            </td>
                        @endforeach
                        <td class="px-3 py-2 font-medium text-gray-900">
                            {{ $reportData->filter(function ($item) use ($verticalHeaderKey, $verticalKey) {
                                    $verticalHeaderField = $item->$verticalHeaderKey instanceof \BackedEnum ? $item->$verticalHeaderKey->value : $item->$verticalHeaderKey;

                                    return $verticalHeaderField == $verticalKey;
                                })->sum('total_cases')
                            }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $rowHeader }}</td>
                        <td class="px-3 py-2 text-gray-700">
                            {{
                                $reportData->filter(fn ($item) => $item->$verticalHeaderKey instanceof \BackedEnum
                                    ? $item->$verticalHeaderKey->value == $verticalKey
                                    : $item->$verticalHeaderKey == $verticalKey
                                )->first()?->total_cases ?? 0
                            }}
                        </td>
                    </tr>
                @endif
            @endforeach

            <tr class="bg-gray-50">
                <td colspan="{{ $firstHeaderElementColSpan }}" class="px-3 py-2 font-semibold text-gray-900">
                    {{ __('report.labels.total') }}
                </td>

                @foreach($subHeader as $key => $subheaderElement)
                    <td class="px-3 py-2 font-semibold text-gray-900">
                        {{ $reportData->filter(function ($item) use ($subHeaderKey, $key) {
                                $subHeaderField = $item->$subHeaderKey instanceof \BackedEnum ? $item->$subHeaderKey->value : $item->$subHeaderKey;

                                return $subHeaderField == $key;
                            })->sum('total_cases')
                        }}
                    </td>
                @endforeach

                <td class="px-3 py-2 font-semibold text-gray-900">
                    {{ $reportData->sum('total_cases') }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
