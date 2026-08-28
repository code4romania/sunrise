@foreach($sections as $section)
    @include('exports.partials.case-report-section', ['section' => $section])
@endforeach

@include('exports.partials.case-report-extra-rows', ['extraRows' => $extraRows ?? []])
@if(($signaturePreparedByManager ?? null) !== null)
    @include('exports.partials.case-report-signature-manager-line', ['managerName' => $signaturePreparedByManager])
@elseif(! empty($signatureRows))
    @include('exports.partials.case-report-signatures', ['signatureRows' => $signatureRows])
@endif
