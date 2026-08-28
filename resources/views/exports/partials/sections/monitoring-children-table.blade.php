@php($rows = $rows ?? [])
<table class="monitoring-children-table">
    <thead>
    <tr>
        <th style="width: 4%;">Nr.</th>
        <th style="width: 14%;">{{ __('monitoring.labels.child_name') }}</th>
        <th style="width: 10%;">{{ __('monitoring.labels.status') }}</th>
        <th style="width: 6%;">{{ __('monitoring.labels.age') }}</th>
        <th style="width: 9%;">{{ __('monitoring.labels.birthdate') }}</th>
        <th style="width: 12%;">{{ __('monitoring.labels.aggressor_relationship') }}</th>
        <th style="width: 11%;">{{ __('monitoring.labels.maintenance_sources') }}</th>
        <th style="width: 12%;">{{ __('monitoring.labels.location') }}</th>
        <th style="width: 22%;">{{ __('monitoring.labels.observations') }}</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $i => $row)
        <tr>
            <td>{{ $i + 1 }}.</td>
            <td>{{ $row['name'] ?? '—' }}</td>
            <td>{{ $row['status'] ?? '—' }}</td>
            <td>{{ $row['age'] ?? '—' }}</td>
            <td>{{ $row['birthdate'] ?? '—' }}</td>
            <td>{{ $row['aggressor_relationship'] ?? '—' }}</td>
            <td>{{ $row['maintenance_sources'] ?? '—' }}</td>
            <td>{{ $row['location'] ?? '—' }}</td>
            <td>{{ $row['observations'] ?? '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="9" style="font-style: italic;">{{ __('monitoring.headings.empty_state_children') }}</td>
        </tr>
    @endforelse
    </tbody>
</table>
