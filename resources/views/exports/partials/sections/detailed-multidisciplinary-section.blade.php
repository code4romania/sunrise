@php($data = $data ?? [])
<div style="margin-left: 16px;">
    <div style="font-size: 14px; font-weight: 700; margin: 4px 0 10px;">
        1. Motivele pentru inițierea evaluării multidisciplinare
    </div>
    <div style="font-size: 12px; margin-bottom: 4px;">
        {!! ($data['is_reported_by'] ?? false) ? '&#9745;' : '&#9633;' !!} semnalare caz de către: {{ $data['reporting_by'] ?? '—' }}
    </div>
    <div style="font-size: 12px; margin-bottom: 4px;">sau</div>
    <div style="font-size: 12px; margin-bottom: 14px;">
        {!! ($data['is_direct_request'] ?? false) ? '&#9745;' : '&#9633;' !!} solicitare servicii prin adresare directă din partea beneficiarului
    </div>

    <div style="font-size: 14px; font-weight: 700; margin: 0 0 8px;">
        2. Istoricul violenței
    </div>
    <table class="detailed-table">
        <thead>
        <tr>
            <th style="width: 20%;">Data</th>
            <th style="width: 80%;">Evenimente semnificative</th>
        </tr>
        </thead>
        <tbody>
        @forelse(($data['violence_history_rows'] ?? []) as $row)
            <tr>
                <td>{{ $row['date'] ?? '—' }}</td>
                <td>{{ $row['significant_events'] ?? '—' }}</td>
            </tr>
        @empty
            <tr>
                <td>—</td>
                <td>—</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="font-size: 14px; font-weight: 700; margin: 12px 0 10px;">
        3. Nevoile beneficiarului
    </div>
    <div style="font-size: 12px; margin-bottom: 8px;">
        a) Din punct de vedere medical: {{ $data['medical_need'] ?? '—' }}
    </div>
    <div style="font-size: 12px; margin-bottom: 8px;">
        b) Din punct de vedere profesional: {{ $data['professional_need'] ?? '—' }}
    </div>
    <div style="font-size: 12px; margin-bottom: 8px;">
        c) Din punct de vedere emoțional și psihologic: {{ $data['emotional_and_psychological_need'] ?? '—' }}
    </div>
    <div style="font-size: 12px; margin-bottom: 8px;">
        d) Din punct de vedere socio-economic: {{ $data['social_economic_need'] ?? '—' }}
    </div>
</div>
