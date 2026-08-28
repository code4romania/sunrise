@php($d = $data ?? [])
<div class="close-file-main-title">{{ __('beneficiary.section.close_file.pdf.document_title') }}</div>

<div class="close-file-field-row">
    <span class="close-file-label">{{ __('beneficiary.section.close_file.pdf.name_label') }}</span>
    <span class="close-file-dotted-value">{{ $d['beneficiary_full_name'] ?? '—' }}</span>
</div>

<div class="close-file-field-row close-file-cnp-row">
    <span class="close-file-label">{{ __('beneficiary.section.close_file.pdf.cnp_label') }}</span>
    @foreach(($d['cnp_digits'] ?? []) as $ch)
        <span class="close-file-cnp-cell">{{ $ch }}</span>
    @endforeach
</div>

<div class="close-file-field-row">
    <span class="close-file-label">{{ __('beneficiary.section.close_file.pdf.admittance_date_cpru') }}</span>
    <span class="close-file-dotted-value">{{ $d['admittance_date'] ?? '—' }}</span>
</div>

<div class="close-file-field-row">
    <span class="close-file-label">{{ __('beneficiary.section.close_file.pdf.exit_date_cpru') }}</span>
    <span class="close-file-dotted-value">{{ $d['exit_date'] ?? '—' }}</span>
</div>

<div class="close-file-subheading">{{ __('beneficiary.section.close_file.pdf.admission_reason_heading') }}</div>
<div class="close-file-bordered">
    @foreach(($d['admittance_reason_rows'] ?? []) as $row)
        <div class="close-file-checkbox-line">
            <label class="close-file-checkbox-label">
                <input type="checkbox" disabled {{ ($row['checked'] ?? false) ? 'checked' : '' }}>
                {{ $row['label'] ?? '' }}
            </label>
        </div>
    @endforeach
</div>
@if(! empty($d['admittance_details']) && ($d['admittance_details'] ?? '—') !== '—')
    <div class="close-file-muted">{{ __('beneficiary.section.close_file.labels.admittance_details') }}: {{ $d['admittance_details'] }}</div>
@endif

<div class="close-file-subheading">{{ __('beneficiary.section.close_file.pdf.close_method_heading') }}</div>
<div class="close-file-bordered">
    @foreach(($d['close_method_rows'] ?? []) as $row)
        <div class="close-file-checkbox-line">
            <label class="close-file-checkbox-label">
                <input type="checkbox" disabled {{ ($row['checked'] ?? false) ? 'checked' : '' }}>
                @if(($row['value'] ?? '') === 'transfer_to')
                    {{ $row['label'] ?? '' }}
                    <span class="close-file-inline-dots">{{ ($d['institution_name'] ?? '—') !== '—' ? $d['institution_name'] : '' }}</span>
                @else
                    {{ $row['label'] ?? '' }}
                @endif
            </label>
        </div>
    @endforeach
    <div class="close-file-extra-line">
        <span class="close-file-label-inline">{{ __('beneficiary.section.close_file.pdf.motiv_label') }}</span>
        <span class="close-file-dotted-value">
            {{ ($d['close_method_selected'] ?? '') === 'beneficiary_request' ? ($d['beneficiary_request'] ?? '') : '' }}
        </span>
    </div>
    <div class="close-file-extra-line">
        <span class="close-file-label-inline">{{ __('beneficiary.section.close_file.pdf.other_situation_label') }}</span>
        <span class="close-file-dotted-value">
            {{ ($d['close_method_selected'] ?? '') === 'other' ? ($d['other_details'] ?? '') : '' }}
        </span>
    </div>
</div>

<div class="close-file-subheading">{{ __('beneficiary.section.close_file.pdf.close_situation_heading') }}</div>
<div class="close-file-situation-box">{{ $d['close_situation'] ?? '—' }}</div>

<div class="close-file-field-row close-file-date-row">
    <span class="close-file-label">{{ __('beneficiary.section.close_file.pdf.closure_sheet_date') }}</span>
    <span class="close-file-dotted-value close-file-date-value">{{ $d['closure_date'] ?? '—' }}</span>
</div>
