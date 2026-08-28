@php($children = $children ?? [])
<table class="children-summary-table">
    <tr>
        <th>Număr copii</th>
        <td>
            Număr de copii ____ {{ $children['total'] ?? 0 }} copii.
            <br>
            Copii în întreținere ____ {{ $children['care'] ?? 0 }} copii,
            din care ____ sub 10 ani {{ $children['sub10'] ?? 0 }},
            ____ între 10 – 18 ani {{ $children['tenTo18'] ?? 0 }},
            peste 18 ani {{ $children['over18'] ?? 0 }}.
        </td>
    </tr>
</table>

<table class="children-table">
    <thead>
    <tr>
        <th style="width: 10%;">Nr. crt.</th>
        <th style="width: 30%;">Nume și prenume copii</th>
        <th style="width: 10%;">Vârsta</th>
        <th style="width: 25%;">Domiciliul actual</th>
        <th style="width: 25%;">Statut/Ocupație</th>
    </tr>
    </thead>
    <tbody>
    @forelse(($children['rows'] ?? []) as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $row['name'] ?? '—' }}</td>
            <td>{{ $row['age'] ?? '—' }}</td>
            <td>{{ $row['current_address'] ?? '—' }}</td>
            <td>{{ $row['status'] ?? '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5">—</td>
        </tr>
    @endforelse
    </tbody>
</table>
