@php($rows = $rows ?? [])
@php($extraRows = $extraRows ?? [])
<table class="risk-factors-table">
    <thead>
    <tr>
        <th>Factor de risc</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $row)
        <tr>
            <td>
                <div class="risk-factor-label">{{ $row['label'] ?? '—' }}</div>
                <div class="risk-factor-value">{{ $row['value'] ?? '—' }}</div>
                <div class="risk-factor-description">{{ $row['description'] ?? '—' }}</div>
            </td>
        </tr>
    @empty
        <tr>
            <td>—</td>
        </tr>
    @endforelse
    </tbody>
</table>

@if(! empty($extraRows))
    <div class="fields-grid">
        @foreach($extraRows as $row)
            <div class="field-item">
                <div class="field-label">{{ $row['label'] }}</div>
                <div class="field-value">{{ $row['value'] }}</div>
            </div>
        @endforeach
    </div>
@endif
