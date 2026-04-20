<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #111827;
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
