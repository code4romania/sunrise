@php($d = $data ?? [])
@php($institutions = $d['institutions'] ?? [])
@php($meetingsRows = $d['meetings_rows'] ?? [])

<div class="psych-sheet-title">FIȘĂ DE CONSILIERE JURIDICĂ</div>
<div style="font-size: 9px; margin-bottom: 8px;">Operator de date cu caracter personal nr.27922</div>

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
        <th>Semnătura</th><td colspan="3">........................................</td>
    </tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 1. Date despre victimă și agresor</div>
<table class="psych-sheet-meta-table">
    <tr><th>Nume și prenume victimă</th><td>{{ $d['beneficiary_name'] ?? '—' }}</td></tr>
    <tr>
        <th>Patrimoniul (locuința)</th>
        <td>
            {!! !empty($d['patrimony_checks']['apartment']) ? '&#9745;' : '&#9633;' !!} Apartament
            &nbsp;{!! !empty($d['patrimony_checks']['house']) ? '&#9745;' : '&#9633;' !!} Casă
            &nbsp;{!! !empty($d['patrimony_checks']['without']) ? '&#9745;' : '&#9633;' !!} Nu deține locuință
        </td>
    </tr>
    <tr>
        <th>Modalitatea deținerii</th>
        <td>
            {!! !empty($d['possession_checks']['exclusive_property']) ? '&#9745;' : '&#9633;' !!} Proprietate exclusivă
            &nbsp;{!! !empty($d['possession_checks']['co_ownership']) ? '&#9745;' : '&#9633;' !!} Coproprietate
            &nbsp;{!! !empty($d['possession_checks']['rental_state_housing']) ? '&#9745;' : '&#9633;' !!} Închiriere locuință de stat
            &nbsp;{!! !empty($d['possession_checks']['private_housing_rental']) ? '&#9745;' : '&#9633;' !!} Închiriere locuință privată
            &nbsp;{!! !empty($d['possession_checks']['commode']) ? '&#9745;' : '&#9633;' !!} Comodat
            &nbsp;{!! !empty($d['possession_checks']['donation']) ? '&#9745;' : '&#9633;' !!} Donație
            &nbsp;{!! !empty($d['possession_checks']['other']) ? '&#9745;' : '&#9633;' !!} Altele: {{ $d['possession_observation'] ?? '—' }}
        </td>
    </tr>
    <tr>
        <th>Acte depuse la dosar</th>
        <td>
            {!! !empty($d['documents_checks']['marriage_certificate']) ? '&#9745;' : '&#9633;' !!} Certificat căsătorie
            &nbsp;{!! !empty($d['documents_checks']['children_birth_certificate']) ? '&#9745;' : '&#9633;' !!} Certificat(e) naștere minor(i)
            &nbsp;{!! !empty($d['documents_checks']['land_deed_extract']) ? '&#9745;' : '&#9633;' !!} Extras CF
            &nbsp;{!! !empty($d['documents_checks']['rental_agreement']) ? '&#9745;' : '&#9633;' !!} Contract închiriere
            &nbsp;{!! !empty($d['documents_checks']['sale_purchase_agreement']) ? '&#9745;' : '&#9633;' !!} Contract vânzare-cumpărare
            &nbsp;{!! !empty($d['documents_checks']['iml_certificate']) ? '&#9745;' : '&#9633;' !!} Certificat IML
            &nbsp;{!! !empty($d['documents_checks']['other']) ? '&#9745;' : '&#9633;' !!} Altele:
            {{ $d['copy_documents_observation'] ?? '—' }} {{ $d['original_documents_observation'] ?? '' }}
        </td>
    </tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 2. Instituții contactate</div>
<table class="psych-sheet-data-table">
    <thead><tr><th>Instituția</th><th>Telefon</th><th>Persoana de contact</th></tr></thead>
    <tbody>
    @foreach($institutions as $row)
        <tr>
            <td>{{ $row['institution'] ?? '—' }}</td>
            <td>{{ $row['phone'] ?? '—' }}</td>
            <td>{{ $row['contact_person'] ?? '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="psych-sheet-subtitle">Secțiunea 3. Ședințe de consiliere juridică</div>
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
        <th style="width:20%;">Mențiuni / Detalii</th>
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
            <td style="white-space: pre-wrap;">{{ $row['details'] ?? ($row['observations'] ?? '—') }}</td>
        </tr>
    @empty
        <tr><td colspan="8" class="monthly-sheet-empty">—</td></tr>
    @endforelse
    </tbody>
</table>

