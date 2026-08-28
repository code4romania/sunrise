<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 220px 24px 24px 24px; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #111827;
        }

        header {
            position: fixed;
            top: -217px;
            left: -24px;
            right: -24px;
        }

        .header-strip {
            height: 3cm;
            background: #8a90a5;
            text-align: center;
        }

        .header-strip img {
            display: block;
            max-height: 3cm;
            max-width: 100%;
            width: auto;
            height: auto;
            margin: 0 auto;
            object-fit: contain;
        }

        .header-title {
            text-align: center;
            margin: 4px 0 0;
            font-size: 11px;
            font-weight: 700;
        }

        h1 {
            font-size: 12px;
            margin: 0 0 10px;
            font-weight: 700;
        }

        .meta {
            margin-bottom: 10px;
            font-size: 8px;
            color: #4b5563;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #374151;
            padding: 3px 4px;
            vertical-align: top;
        }
    </style>
</head>
<body>
<header>
    <div class="header-strip">
        @php($headerSrc = $branding['header_src'] ?? $branding['header_url'] ?? null)
        @if(! empty($headerSrc))
            <img src="{{ $headerSrc }}" alt="header">
        @endif
    </div>
    <p class="header-title">{{ $branding['name'] ?? config('app.name') }}</p>
</header>
<h1>{{ $title }}</h1>
<p class="meta">
    {{ __('report.labels.start_date') }}:
    {{ $exportPeriodStart ? \Illuminate\Support\Carbon::parse($exportPeriodStart)->format('d/m/Y') : '' }}
    &mdash;
    {{ __('report.labels.end_date') }}:
    {{ $exportPeriodEnd ? \Illuminate\Support\Carbon::parse($exportPeriodEnd)->format('d/m/Y') : '' }}
</p>
@include('exports.report-table')
</body>
</html>
