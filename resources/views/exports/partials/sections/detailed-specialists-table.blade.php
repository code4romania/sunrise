@php($rows = $rows ?? [])
<table class="detailed-table">
    <thead>
    <tr>
        <th style="width: 8%;">Nr. Crt.</th>
        <th style="width: 23%;">Numele și prenumele</th>
        <th style="width: 19%;">Instituția</th>
        <th style="width: 21%;">Relația cu copilul/familia</th>
        <th style="width: 29%;">Data la care a fost contactată</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $index => $row)
        <tr>
            <td>{{ $index + 1 }}.</td>
            <td>{{ $row['full_name'] ?? '—' }}</td>
            <td>{{ $row['institution'] ?? '—' }}</td>
            <td>{{ $row['relationship'] ?? '—' }}</td>
            <td>{{ $row['date'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="5">—</td></tr>
    @endforelse
    </tbody>
</table>
