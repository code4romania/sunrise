@if(! empty($extraRows))
    <div class="section-title">Informații suplimentare</div>
    <div class="fields-grid">
        @foreach($extraRows as $row)
            <div class="field-item">
                <div class="field-label">{{ $row['label'] }}</div>
                <div class="field-value">{{ $row['value'] }}</div>
            </div>
        @endforeach
    </div>
@endif
