@php($d = $data ?? [])
@php($section27Rows = $d['section_27_rows'] ?? [])
@php($meetingsRows = $d['meetings_rows'] ?? [])

<div class="psych-sheet-title">FIȘĂ DE CONSILIERE PSIHOLOGICĂ</div>
<table class="psych-sheet-meta-table">
    <tr>
        <th>Instituția</th><td>{{ $d['service_name'] ?? '—' }}</td>
        <th>Data întocmirii fișei</th><td>{{ $d['sheet_date'] ?? '—' }}</td>
    </tr>
    <tr>
        <th>Numele consilierului</th><td>{{ $d['specialist_name'] ?? '—' }}</td>
        <th>Număr înregistrare caz</th><td>{{ $d['case_number'] ?? '—' }}</td>
    </tr>
    <tr>
        <th>Semnătura</th><td>...............................</td>
        <th>Numărul ședinței</th><td>{{ $d['session_number'] ?? '—' }}</td>
    </tr>
    <tr>
        <th>Durata</th>
        <td colspan="3">
            {!! !empty($d['duration_60']) ? '&#9745;' : '&#9633;' !!} 60min
            &nbsp;&nbsp;{!! !empty($d['duration_90']) ? '&#9745;' : '&#9633;' !!} 90min
            &nbsp;&nbsp;{!! !empty($d['duration_120']) ? '&#9745;' : '&#9633;' !!} 120min
        </td>
    </tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 1. Date despre victimă și copii</div>
<table class="psych-sheet-meta-table">
    <tr><th>Nume și prenume</th><td colspan="3">{{ $d['beneficiary_name'] ?? '—' }}</td></tr>
    <tr>
        <th>Stare civilă</th>
        <td colspan="3">
            {!! !empty($d['civil_single']) ? '&#9745;' : '&#9633;' !!} Necăsătorit
            &nbsp;&nbsp;{!! !empty($d['civil_cohabitation']) ? '&#9745;' : '&#9633;' !!} Uniune consensuală
            &nbsp;&nbsp;{!! !empty($d['civil_married']) ? '&#9745;' : '&#9633;' !!} Căsătorit
            &nbsp;&nbsp;{!! !empty($d['civil_divorced']) ? '&#9745;' : '&#9633;' !!} Divorțat
            &nbsp;&nbsp;{!! !empty($d['civil_widowed']) ? '&#9745;' : '&#9633;' !!} Văduv
        </td>
    </tr>
    <tr><th>Număr de copii</th><td colspan="3">{{ $d['children_count'] ?? '0' }}</td></tr>
</table>
<table class="psych-sheet-data-table">
    <thead><tr><th style="width:6%;">Nr.</th><th style="width:34%;">Nume</th><th style="width:10%;">Vârsta</th><th style="width:35%;">Unitatea de învățământ/Locul de muncă</th><th style="width:15%;">Clasa</th></tr></thead>
    <tbody>
    @foreach(($d['children_rows'] ?? []) as $i => $child)
        <tr><td>{{ $i + 1 }}</td><td>{{ $child['name'] ?? '' }}</td><td>{{ $child['age'] ?? '' }}</td><td>{{ $child['school_or_work'] ?? '' }}</td><td>{{ $child['class'] ?? '' }}</td></tr>
    @endforeach
    </tbody>
</table>

<div class="psych-sheet-subtitle">Secțiunea 2. Date medicale</div>
<table class="psych-sheet-meta-table">
    <tr>
        <th>Consum de substanțe</th>
        <td colspan="3">
            {!! !empty($d['drug_alcohol_occasional']) ? '&#9745;' : '&#9633;' !!} Alcool ocazional
            &nbsp;{!! !empty($d['drug_alcohol_frequent']) ? '&#9745;' : '&#9633;' !!} Alcool frecvent
            &nbsp;{!! !empty($d['drug_tobacco']) ? '&#9745;' : '&#9633;' !!} Tutun
            &nbsp;{!! !empty($d['drug_tranquilizers']) ? '&#9745;' : '&#9633;' !!} Tranchilizante
            &nbsp;{!! !empty($d['drug_drugs']) ? '&#9745;' : '&#9633;' !!} Droguri
            &nbsp;{!! !empty($d['drug_other']) ? '&#9745;' : '&#9633;' !!} Alte ({{ $d['drug_other_text'] ?? '—' }})
        </td>
    </tr>
    <tr><th>Contracepție curentă</th><td colspan="3">{!! !empty($d['current_contraception_no']) ? '&#9745;' : '&#9633;' !!} Nu &nbsp; {!! !empty($d['current_contraception_yes']) ? '&#9745;' : '&#9633;' !!} Da &nbsp; Specificați: {{ $d['current_contraception_text'] ?? '—' }}</td></tr>
    <tr><th>Antecedente psihiatrice</th><td colspan="3">{!! !empty($d['psychiatric_history_no']) ? '&#9745;' : '&#9633;' !!} Nu &nbsp; {!! !empty($d['psychiatric_history_yes']) ? '&#9745;' : '&#9633;' !!} Da &nbsp; Specificați: {{ $d['psychiatric_history_text'] ?? '—' }}</td></tr>
    <tr><th>Investigații pentru patologie psihiatrică</th><td colspan="3">{!! !empty($d['investigations_no']) ? '&#9745;' : '&#9633;' !!} Nu &nbsp; {!! !empty($d['investigations_yes']) ? '&#9745;' : '&#9633;' !!} Da &nbsp; Specificați: {{ $d['investigations_text'] ?? '—' }}</td></tr>
    <tr><th>Tratamente curente pentru patologie psihiatrică</th><td colspan="3">{!! !empty($d['treatment_no']) ? '&#9745;' : '&#9633;' !!} Nu &nbsp; {!! !empty($d['treatment_yes']) ? '&#9745;' : '&#9633;' !!} Da &nbsp; Specificați: {{ $d['treatment_text'] ?? '—' }}</td></tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 3. Istoricul relației</div>
<div class="psych-sheet-box">{{ $d['section_3'] ?? '—' }}</div>
<div class="psych-sheet-subtitle">Secțiunea 4. Descrierea ultimului incident</div>
<div class="psych-sheet-box">{{ $d['section_4'] ?? '—' }}</div>
<div class="psych-sheet-subtitle">Secțiunea 5. Istoricul formelor de violență</div>
<div class="psych-sheet-box">{{ $d['section_5'] ?? '—' }}</div>

<div class="psych-sheet-subtitle">Secțiunea 6. Frecvența formelor de violență</div>
<table class="psych-sheet-data-table">
    <thead><tr><th>Forma</th><th>Zilnic</th><th>Săptămânal</th><th>Lunar</th><th>Mai rar decât lunar</th></tr></thead>
    <tbody>
    @foreach(($d['frequency_rows'] ?? []) as $row)
        <tr>
            <td>{{ $row['label'] }}</td>
            <td>{!! !empty($row['daily']) ? '&#9745;' : '&#9633;' !!}</td>
            <td>{!! !empty($row['weekly']) ? '&#9745;' : '&#9633;' !!}</td>
            <td>{!! !empty($row['monthly']) ? '&#9745;' : '&#9633;' !!}</td>
            <td>{!! !empty($row['rare']) ? '&#9745;' : '&#9633;' !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="psych-sheet-subtitle">Secțiunea 7. Descrierea formelor de violență</div>
<table class="psych-sheet-meta-table">
    <tr><th>Fizică</th><td>{{ $d['description_physical'] ?? '—' }}</td></tr>
    <tr><th>Sexuală</th><td>{{ $d['description_sexual'] ?? '—' }}</td></tr>
    <tr><th>Psihologică (Emoțională)</th><td>{{ $d['description_psychological'] ?? '—' }}</td></tr>
    <tr><th>Verbală</th><td>{{ $d['description_verbal'] ?? '—' }}</td></tr>
    <tr><th>Socială</th><td>{{ $d['description_social'] ?? '—' }}</td></tr>
    <tr><th>Economică</th><td>{{ $d['description_economic'] ?? '—' }}</td></tr>
    <tr><th>Cibernetică</th><td>{{ $d['description_cyber'] ?? '—' }}</td></tr>
    <tr><th>Spirituală</th><td>{{ $d['description_spiritual'] ?? '—' }}</td></tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 8. Efecte ale violenței</div>
<table class="psych-sheet-meta-table">
    <tr><th>Plan fizic</th><td>{{ $d['effects_physical'] ?? '—' }}</td></tr>
    <tr><th>Plan psihic</th><td>{{ $d['effects_psychological'] ?? '—' }}</td></tr>
    <tr><th>Plan social</th><td>{{ $d['effects_social'] ?? '—' }}</td></tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 9. Factori de risc</div>
<div class="psych-sheet-box">{{ $d['section_9'] ?? '—' }}</div>
<div class="psych-sheet-subtitle">Secțiunea 10. Factori de protecție</div>
<table class="psych-sheet-meta-table">
    <tr><th>Resurse interne</th><td>{{ $d['section_10_internal'] ?? '—' }}</td></tr>
    <tr><th>Resurse externe</th><td>{{ $d['section_10_external'] ?? '—' }}</td></tr>
</table>
<div class="psych-sheet-subtitle">Secțiunea 11. Solicitări</div>
<div class="psych-sheet-box">{{ $d['section_11'] ?? '—' }}</div>
<div class="psych-sheet-subtitle">Secțiunea 12. Evaluare psihologică la prima ședință</div>
<div class="psych-sheet-box" style="min-height: 120px;">{{ $d['section_12'] ?? '—' }}</div>

<div class="psych-sheet-subtitle">Secțiunea 13. Plan de intervenție / recomandări</div>
<div class="psych-sheet-box">{{ $d['section_13'] ?? '—' }}</div>
<div class="psych-sheet-subtitle">Secțiunea 14. Plan de siguranță</div>
<div style="font-size: 10px; margin-bottom: 8px;">{!! !empty($d['section_14_no']) ? '&#9745;' : '&#9633;' !!} Nu &nbsp;&nbsp; {!! !empty($d['section_14_yes']) ? '&#9745;' : '&#9633;' !!} Da</div>
<div class="psych-sheet-subtitle">Secțiunea 15. Recomandări</div>
<div class="psych-sheet-box">{{ $d['section_15'] ?? '—' }}</div>

<table class="psych-sheet-meta-table">
    <tr><th style="width:25%;">Programare - Data</th><td style="width:25%;">{{ $d['schedule_date'] ?? '—' }}</td><th style="width:25%;">Ora</th><td style="width:25%;">{{ $d['schedule_time'] ?? '—' }}</td></tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 27. Plan de consiliere / intervenție / detaliere</div>
<table class="psych-sheet-data-table">
    <thead><tr><th style="width:6%;">Nr.</th><th style="width:12%;">Data</th><th style="width:10%;">Număr ședință</th><th style="width:22%;">Intervenție</th><th style="width:50%;">Rezumat</th></tr></thead>
    <tbody>
    @forelse($section27Rows as $row)
        <tr>
            <td>{{ $row['nr'] ?? '—' }}</td>
            <td>{{ $row['date'] ?? '—' }}</td>
            <td>{{ $row['session_number'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['intervention_name'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['summary'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="monthly-sheet-empty">—</td></tr>
    @endforelse
    </tbody>
</table>

<div class="psych-sheet-subtitle">EVIDENȚA ȘEDINȚELOR DE CONSILIERE PSIHOLOGICĂ</div>
<table class="psych-sheet-data-table">
    <thead>
    <tr>
        <th style="width:5%;">Nr.</th>
        <th style="width:10%;">Data</th>
        <th style="width:8%;">Ora</th>
        <th style="width:16%;">Intervenție</th>
        <th style="width:14%;">Specialist</th>
        <th style="width:9%;">Durată</th>
        <th style="width:18%;">Teme</th>
        <th style="width:20%;">Mențiuni</th>
    </tr>
    </thead>
    <tbody>
    @forelse($meetingsRows as $row)
        <tr>
            <td>{{ $row['nr'] ?? '—' }}</td>
            <td>{{ $row['date'] ?? '—' }}</td>
            <td>{{ $row['time'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['intervention_name'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['specialist'] ?? '—' }}</td>
            <td>{{ $row['duration'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['topic'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['observations'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="8" class="monthly-sheet-empty">—</td></tr>
    @endforelse
    </tbody>
</table>

