@php($d = $data ?? [])
@php($institutions = $d['institutions'] ?? [])
@php($matrixRows = $d['matrix_rows'] ?? [])
@php($columns = $d['matrix_columns'] ?? range(1, 10))
@php($detailsRows = $d['section4_rows'] ?? [])

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
        <th style="width: 26%;">Element</th>
        @foreach($columns as $col)
            <th style="width: 7.4%;">{{ $col }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($matrixRows as $row)
        <tr>
            <td style="white-space: pre-wrap;">{{ $row['label'] ?? '—' }}</td>
            @foreach(($row['values'] ?? []) as $value)
                <td style="text-align:center;">{{ $value }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

<div class="psych-sheet-subtitle">Secțiunea 4. Detalii / ședință</div>
@foreach($detailsRows as $row)
    <table class="psych-sheet-meta-table">
        <tr><th style="width: 20%;">Detalii</th><td>{{ $row['details'] ?? '—' }}</td></tr>
        <tr><th>Numărul ședinței</th><td>{{ $row['session_number'] ?? '—' }}</td></tr>
        <tr><th>Programare</th><td>Data {{ $row['schedule_date'] ?? '—' }} &nbsp;&nbsp; Ora {{ $row['schedule_time'] ?? '—' }}</td></tr>
    </table>
@endforeach

