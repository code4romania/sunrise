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
    <div style="font-size: 12px; margin-bottom: 8px;">
        e) Din punct de vedere juridic: {{ $data['legal_needs'] ?? '—' }}
    </div>

    <div style="font-size: 14px; font-weight: 700; margin: 12px 0 10px;">
        4. Factori de mediu şi specifici familiei
    </div>
    <div style="font-size: 12px; margin-bottom: 8px;">
        a) Familia lărgită: {{ $data['extended_family'] ?? '—' }}
    </div>
    <div style="font-size: 12px; margin-bottom: 8px;">
        b) Integrarea socială: {{ $data['family_social_integration'] ?? '—' }}
    </div>
    <div style="font-size: 12px; margin-bottom: 8px;">
        c) Venit: {{ $data['income'] ?? '—' }}
    </div>
    <div style="font-size: 12px; margin-bottom: 8px;">
        d) Resurse comunitare: {{ $data['community_resources'] ?? '—' }}
    </div>
    <div style="font-size: 12px; margin-bottom: 8px;">
        e) Locuinţa: {{ $data['house'] ?? '—' }}
    </div>
    <div style="font-size: 12px; margin-bottom: 8px;">
        f) Loc de muncă: {{ $data['workplace'] ?? '—' }}
    </div>

    <div style="font-size: 14px; font-weight: 700; margin: 12px 0 10px;">
        5. Riscuri: (se vor menționa acele evenimente/contexte ce pot conduce la situații de criză cu intervenție imediată)
    </div>
    <div style="font-size: 12px; margin-bottom: 10px; white-space: pre-wrap;">
        {{ $data['risk'] ?? '—' }}
    </div>

    <div style="font-size: 14px; font-weight: 700; margin: 12px 0 10px;">
        VI. Rezultatele evaluării: (se vor menționa serviciile propuse pentru a beneficia în perioada următoare)
    </div>
    <div style="font-size: 12px; font-weight: 700; margin-bottom: 8px;">
        Recomandări pentru planul de intervenție:
    </div>

    <div style="font-size: 12px; margin-bottom: 4px;">{!! ($data['recommended_psychological'] ?? false) ? '&#9745;' : '&#9633;' !!} Servicii de consiliere psihologică</div>
    <div style="font-size: 12px; margin-bottom: 4px;">{!! ($data['recommended_social'] ?? false) ? '&#9745;' : '&#9633;' !!} Servicii de asistență socială</div>
    <div style="font-size: 12px; margin-bottom: 4px;">{!! ($data['recommended_legal'] ?? false) ? '&#9745;' : '&#9633;' !!} Servicii de consiliere juridică</div>
    <div style="font-size: 12px; margin-bottom: 4px;">{!! ($data['recommended_shelter'] ?? false) ? '&#9745;' : '&#9633;' !!} Adăpostire-îngrijire și protecție</div>
    <div style="font-size: 12px; margin-bottom: 4px;">{!! ($data['recommended_reintegration'] ?? false) ? '&#9745;' : '&#9633;' !!} Integrare/ reintegrare socială</div>
    <div style="font-size: 12px; margin-bottom: 4px;">{!! ($data['recommended_medical'] ?? false) ? '&#9745;' : '&#9633;' !!} Facilitare acces medical</div>
    <div style="font-size: 12px; margin-bottom: 8px;">{!! ($data['recommended_other'] ?? false) ? '&#9745;' : '&#9633;' !!} Alte servicii de specialitate</div>
    @if(($data['other_services_description'] ?? '—') !== '—')
        <div style="font-size: 12px; margin-bottom: 8px; white-space: pre-wrap;">
            {{ $data['other_services_description'] }}
        </div>
    @endif
    @if(($data['recommendations_for_intervention_plan'] ?? '—') !== '—')
        <div style="font-size: 12px; margin-bottom: 8px; white-space: pre-wrap;">
            {{ $data['recommendations_for_intervention_plan'] }}
        </div>
    @endif
</div>
