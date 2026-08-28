@php($rows = $rows ?? [])
<table class="detailed-table">
    <thead>
    <tr>
        <th style="width: 25%;">Interval</th>
        <th style="width: 75%;">Evenimente semnificative</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $row)
        <tr>
            <td>{{ $row['date_interval'] ?? '—' }}</td>
            <td>{{ $row['significant_events'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="2">—</td></tr>
    @endforelse
    </tbody>
</table>
