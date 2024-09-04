@php
    use Filament\Support\Enums\Alignment;
@endphp


<table>
    <tr>
        @foreach($header as $key => $headerElement)
            @if($key === 0)
                <td colspan="{{ $firstHeaderElementColSpan }}" rowspan="{{ $firstHeaderElementRowSpan }}">
                    {{ $headerElement }}
                </td>
            @elseif($key === 1 && $subHeader)

                <td colspan="{{ count($subHeader) }}">
                    {{ $headerElement }}
                </td>

            @else
                <td rowspan="{{ $firstHeaderElementRowSpan }}">
                    {{ $headerElement }}
                </td>
            @endif
        @endforeach
    </tr>

    @if($subHeader)
        <tr>
            @foreach($subHeader as $subheaderElement)
                <td>
                    {{ $subheaderElement }}
                </td>
            @endforeach
        </tr>
    @endif


    @foreach($verticalHeader as $verticalKey => $header)
        @if ($verticalSubHeader)
            @php
                $firstSubHeaderRow = true
            @endphp
            @foreach($verticalSubHeader as $verticalSubKey => $subheader)
                <tr>
                    @if ($firstSubHeaderRow)
                        <td rowspan="{{ count($verticalSubHeader) }}">
                            {{ $header }}
                        </td>
                    @endif

                    <td>
                        {{ $subheader }}
                    </td>

                    @foreach($subHeader as $key => $subheaderElement)
                        <td>
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

                    <td>
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
                <td>
                    {{ $header }}
                </td>
                @foreach($subHeader as $key => $subheaderElement)
                    <td>
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
                <td>
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
                <td>
                    {{ $header }}
                </td>

                <td>
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
        <td colspan="{{ $firstHeaderElementColSpan }}">
            {{ __('report.labels.total') }}
        </td>

        @foreach($subHeader as $key => $subheaderElement)
            <td>
                {{ $reportData->filter(function($item) use ($subHeaderKey, $key){
                        $subHeaderField = $item->$subHeaderKey instanceof BackedEnum ? $item->$subHeaderKey->value : $item->$subHeaderKey;
                        return $subHeaderField == $key;
                    })->sum('total_cases') }}
            </td>
        @endforeach

        <td>
            {{ $reportData->sum('total_cases') }}
        </td>
    </tr>


</table>
