@php($data = $data ?? [])
@php($b = $data['beneficiary'] ?? [])
@php($p = $data['partner'] ?? [])
@php($c = $data['children'] ?? [])
<div class="detailed-first-page-title">FIȘA DE EVALUARE DETALIATĂ</div>

<div class="detailed-first-heading">I. Date personale privind beneficiarul:</div>
<table class="detailed-first-table">
    <tr><th>Nume și prenumele:</th><td>{{ $b['full_name'] ?? '—' }}</td></tr>
    <tr><th>CNP:</th><td>{{ $b['cnp'] ?? '—' }}</td></tr>
    <tr><th>Data și locul nașterii:</th><td>{{ $b['birth'] ?? '—' }}</td></tr>
    <tr><th>Domiciliul legal:</th><td>{{ $b['legal_address'] ?? '—' }}</td></tr>
    <tr><th>Domiciliul efectiv:</th><td>{{ $b['effective_address'] ?? '—' }}</td></tr>
    <tr><th>Pregătirea școlară/profesională:</th><td>{{ $b['studies'] ?? '—' }}</td></tr>
    <tr><th>Ocupația:</th><td>{{ $b['occupation'] ?? '—' }}</td></tr>
    <tr><th>Telefon:</th><td>{{ $b['phone'] ?? '—' }}</td></tr>
    <tr><th>Observații:</th><td>{{ $b['observations'] ?? '—' }}</td></tr>
</table>

<div class="detailed-first-heading">II.Date personale privind agresorul și membrii familiei:</div>
<div class="detailed-first-subheading">Soțul/Partenerul/Tipul relației (căsătorie, divorț, separare, concubinaj, etc.):</div>
<table class="detailed-first-table">
    <tr><th>Nume și prenume:</th><td>{{ $p['full_name'] ?? '—' }}</td></tr>
    <tr><th>Vârsta:</th><td>{{ $p['age'] ?? '—' }}</td></tr>
    <tr><th>Ocupația:</th><td>{{ $p['occupation'] ?? '—' }}</td></tr>
    <tr><th>Domiciliul legal:</th><td>{{ $p['legal_address'] ?? '—' }}</td></tr>
    <tr><th>Domiciliul efectiv:</th><td>{{ $p['effective_address'] ?? '—' }}</td></tr>
    <tr><th>Observații:</th><td>{{ $p['observations'] ?? '—' }}</td></tr>
</table>

<div class="detailed-first-heading">Copii:</div>
<table class="detailed-first-table">
    <tr><th>Număr total copii</th><td>{{ $c['total'] ?? 0 }}</td></tr>
    <tr><th>Număr copii care însoțesc beneficiara</th><td>{{ $c['accompanying'] ?? 0 }}</td></tr>
</table>

<table class="detailed-first-children-table">
    <thead>
    <tr>
        <th style="width: 8%;">Nr. crt.</th>
        <th style="width: 24%;">Nume și prenume copii</th>
        <th style="width: 9%;">Vârstă</th>
        <th style="width: 17%;">Domiciliul actual</th>
        <th style="width: 22%;">Statut/Ocupație</th>
        <th style="width: 20%;">Observații</th>
    </tr>
    </thead>
    <tbody>
    @foreach(($c['rows'] ?? []) as $i => $childRow)
        <tr>
            <td>{{ $i + 1 }}.</td>
            <td>{{ $childRow['name'] ?? '' }}</td>
            <td>{{ $childRow['age'] ?? '' }}</td>
            <td>{{ $childRow['current_address'] ?? '' }}</td>
            <td>{{ $childRow['status'] ?? '' }}</td>
            <td>{{ $childRow['observations'] ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="page-break-after"></div>
