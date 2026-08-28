<!doctype html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    @include('exports.partials.case-report-styles')
</head>
<body>
@include('exports.partials.case-report-header')
@include('exports.partials.case-report-footer')

<main>
    @yield('case_report_main')
</main>

@include('exports.partials.case-report-page-numbers')
</body>
</html>
