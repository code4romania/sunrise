@php($topRows = $topRows ?? [])
@php($measureBlocks = $measureBlocks ?? [])
<table class="monitoring-label-value-table">
    @foreach($topRows as $row)
        <tr>
            <th>{{ $row['label'] ?? '—' }}</th>
            <td style="white-space: pre-wrap;">{{ $row['value'] ?? '—' }}</td>
        </tr>
    @endforeach
</table>

@foreach($measureBlocks as $block)
    <div class="monitoring-subheading">{{ $block['heading'] ?? '' }}</div>
    <table class="monitoring-label-value-table">
        @foreach(($block['rows'] ?? []) as $row)
            <tr>
                <th>{{ $row['label'] ?? '—' }}</th>
                <td style="white-space: pre-wrap;">{{ $row['value'] ?? '—' }}</td>
            </tr>
        @endforeach
    </table>
@endforeach
