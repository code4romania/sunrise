@php($rows = $rows ?? [])
<table class="detailed-table">
    <thead>
    <tr>
        <th style="width: 28%;">Nume/prenume</th>
        <th style="width: 18%;">Data</th>
        <th style="width: 54%;">Locul</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $row)
        <tr>
            <td>{{ $row['specialist'] ?? '—' }}</td>
            <td>{{ $row['date'] ?? '—' }}</td>
            <td>{{ $row['location'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="3">—</td></tr>
    @endforelse
    </tbody>
</table>
