@foreach($sections as $section)
    @include('exports.partials.case-report-section', ['section' => $section])
@endforeach

@include('exports.partials.case-report-extra-rows', ['extraRows' => $extraRows ?? []])
@include('exports.partials.case-report-signatures', ['signatureRows' => $signatureRows ?? []])
