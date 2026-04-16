@php($managerName = trim((string) ($managerName ?? '')))
<div class="close-file-signature-page">
    <p class="close-file-signature-line">
        {{ __('beneficiary.section.close_file.pdf.prepared_by') }}
        <strong>{{ __('beneficiary.section.close_file.pdf.case_manager_role') }}</strong>
        @if($managerName !== '')
            <span class="close-file-signature-name">{{ $managerName }}</span>
        @endif
        <span class="close-file-signature-dots"></span>
    </p>
</div>
