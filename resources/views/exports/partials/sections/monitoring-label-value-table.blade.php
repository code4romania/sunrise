@php($rows = $rows ?? [])
<table class="monitoring-label-value-table">
    @foreach($rows as $row)
        <tr>
            <th>{{ $row['label'] ?? '—' }}</th>
            <td style="white-space: pre-wrap;">{{ $row['value'] ?? '—' }}</td>
        </tr>
    @endforeach
</table>
