<!doctype html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 128px 34px 66px 34px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 10px; }
        header { position: fixed; top: -125px; left: -34px; right: -34px; }
        footer { position: fixed; bottom: -56px; left: -34px; right: -34px; font-size: 9px; color: #4b5563; }
        .header-strip {
            height: 54px;
            background: #8a90a5;
            text-align: center;
            color: #ffffff;
        }
        .header-strip img {
            height: 54px;
            width: 100%;
            object-fit: cover;
        }
        .header-content {
            text-align: center;
            background: #ffffff;
            padding: 10px 16px 0 16px;
        }
        .title { font-size: 20px; font-weight: 700; margin: 0 0 2px 0; }
        .meta { font-size: 12px; color: #111827; margin: 0; font-weight: 600; }
        .section-title { margin: 16px 0 8px; font-size: 14px; font-weight: 700; color: #1f2937; }
        .fields-grid { margin-bottom: 8px; }
        .field-item {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            margin: 0 2% 10px 0;
            page-break-inside: avoid;
        }
        .field-item:nth-child(2n) { margin-right: 0; }
        .field-label {
            font-size: 10px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 2px;
        }
        .field-value {
            font-size: 11px;
            color: #374151;
            min-height: 14px;
            line-height: 1.2;
        }
        .footer-strip {
            background: #d5cdef;
            padding: 6px 14px;
        }
        .footer-cell {
            display: inline-block;
            vertical-align: middle;
        }
        .footer-left { width: 34%; text-align: left; }
        .footer-center { width: 40%; text-align: center; }
        .footer-right { width: 24%; text-align: right; }
        .footer-logo { height: 14px; }
        .signature-title { margin-top: 16px; margin-bottom: 6px; font-size: 11px; font-weight: 700; }
        .signature-table { width: 100%; border-collapse: collapse; }
        .signature-table th, .signature-table td {
            border: 1px solid #d1d5db;
            padding: 6px;
            font-size: 10px;
            text-align: left;
        }
        .signature-table th { background: #f3f4f6; }
    </style>
</head>
<body>
<header>
    <div class="header-strip">
        @if(! empty($branding['header_url']))
            <img src="{{ $branding['header_url'] }}" alt="header">
        @endif
    </div>
    <div class="header-content">
        <h1 class="title">{{ $reportTitle }}</h1>
        <p class="meta">Număr caz {{ $caseId }}</p>
    </div>
</header>

<footer>
    <div class="footer-strip">
        <span class="footer-cell footer-left">
                @if(! empty($branding['logo_url']))
                <img src="{{ $branding['logo_url'] }}" alt="logo" class="footer-logo">
                @endif
        </span>
        <span class="footer-cell footer-center">Printat la {{ $branding['printed_at'] }}</span>
        <span class="footer-cell footer-right"></span>
    </div>
</footer>

<main>
    @foreach($sections as $section)
        <div class="section-title">{{ $section['title'] }}</div>
        <div class="fields-grid">
            @forelse($section['rows'] as $row)
                <div class="field-item">
                    <div class="field-label">{{ $row['label'] }}</div>
                    <div class="field-value">{{ $row['value'] }}</div>
                </div>
            @empty
                <div class="field-item">
                    <div class="field-label">Valoare</div>
                    <div class="field-value">—</div>
                </div>
            @endforelse
        </div>
    @endforeach

    @if(! empty($extraRows))
        <div class="section-title">Informații suplimentare</div>
        <div class="fields-grid">
            @foreach($extraRows as $row)
                <div class="field-item">
                    <div class="field-label">{{ $row['label'] }}</div>
                    <div class="field-value">{{ $row['value'] }}</div>
                </div>
            @endforeach
        </div>
    @endif

    @if(! empty($signatureRows))
        <div class="signature-title">Membri echipă de caz - semnături</div>
        <table class="signature-table">
            <thead>
            <tr>
                <th>Nume</th>
                <th>Rol</th>
                <th>Semnătură</th>
            </tr>
            </thead>
            <tbody>
            @foreach($signatureRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['role'] }}</td>
                    <td style="height: 28px;"></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</main>

<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
        $pdf->page_text(500, 818, 'Pagina {PAGE_NUM}/{PAGE_COUNT}', $font, 9, [75 / 255, 85 / 255, 99 / 255]);
    }
</script>
</body>
</html>
