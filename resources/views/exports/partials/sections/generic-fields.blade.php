@php($rows = $rows ?? [])
<div class="fields-grid">
    @forelse($rows as $row)
        <div class="field-item">
            <div class="field-label">{{ $row['label'] }}</div>
            <div class="field-value">{{ $row['value'] }}</div>
        </div>
    @empty
        <div class="field-item">
            <div class="field-label">Valoare</div>
            <div class="field-value">—</div>
        </div>
    @endforelse
</div>
