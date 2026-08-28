@if(! empty($signatureRows))
    <div class="signature-title">Membri echipă de caz - semnături</div>
    <table class="signature-table">
        <thead>
        <tr>
            @php($hasRoles = collect($signatureRows)->contains(fn ($row) => ! empty($row['role'] ?? '')))
            <th>Nume</th>
            @if($hasRoles)
                <th>Rol</th>
            @endif
            <th>Semnătură</th>
        </tr>
        </thead>
        <tbody>
        @foreach($signatureRows as $row)
            <tr>
                <td>{{ $row['name'] }}</td>
                @if($hasRoles)
                    <td>{{ $row['role'] }}</td>
                @endif
                <td style="height: 28px;"></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
