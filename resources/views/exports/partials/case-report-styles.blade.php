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

    .risk-factors-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    .risk-factors-table th, .risk-factors-table td {
        border: 1px solid #d1d5db;
        padding: 6px;
        font-size: 10px;
        vertical-align: top;
    }
    .risk-factors-table th { background: #f3f4f6; }
    .risk-factor-label { font-weight: 700; margin-bottom: 2px; }
    .risk-factor-value { margin-bottom: 4px; }
    .risk-factor-description { font-size: 10px; color: #374151; }

    .identity-wrapper { margin-bottom: 8px; }
    .identity-form-table {
        width: 100%;
        border: 1px solid #000;
        border-collapse: collapse;
    }
    .identity-cell {
        border: 1px solid #000;
        padding: 6px;
        vertical-align: top;
    }
    .identity-label {
        font-size: 10px;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .identity-value {
        font-size: 11px;
        min-height: 14px;
        line-height: 1.2;
    }
    .identity-subsection-title { font-size: 10px; font-weight: 700; margin-bottom: 4px; }
    .identity-checkbox-row { margin-top: 2px; }
    .identity-checkbox-wrap { margin-top: 2px; }
    .identity-checkbox-label {
        font-size: 10px;
        display: block;
        margin-right: 0;
        margin-bottom: 2px;
        vertical-align: top;
        line-height: 1.3;
    }
    .identity-checkbox-label input {
        vertical-align: middle;
        margin-right: 8px;
    }
    .identity-inline-row { margin-top: 6px; }
    .identity-inline-label { display: inline-block; min-width: 28px; font-weight: 700; }
    .identity-inline-value { display: inline-block; margin-right: 14px; }
    .identity-muted { color: #374151; }
    .identity-subsection-spacer { height: 6px; }

    .children-summary-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-bottom: 8px;
    }
    .children-summary-table th,
    .children-summary-table td {
        border: 1px solid #000;
        padding: 6px;
        font-size: 10px;
        vertical-align: top;
    }
    .children-summary-table th {
        width: 20%;
        font-weight: 700;
        text-align: left;
    }

    .children-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
    }
    .children-table th,
    .children-table td {
        border: 1px solid #000;
        padding: 6px;
        font-size: 10px;
        vertical-align: top;
    }
    .children-table th { font-weight: 700; background: #fff; }
    .detailed-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-bottom: 8px;
    }
    .detailed-table th,
    .detailed-table td {
        border: 1px solid #000;
        padding: 6px;
        font-size: 10px;
        vertical-align: top;
    }
    .detailed-table th { font-weight: 700; background: #fff; }
    .detailed-first-page-title {
        font-size: 34px;
        font-weight: 700;
        text-align: center;
        margin: 6px 0 20px;
    }
    .detailed-first-heading {
        font-size: 13px;
        font-weight: 700;
        margin: 8px 0 2px;
    }
    .detailed-first-subheading {
        font-size: 13px;
        font-weight: 700;
        margin: 0 0 4px;
    }
    .detailed-first-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-bottom: 10px;
    }
    .detailed-first-table th,
    .detailed-first-table td {
        border: 1px solid #000;
        padding: 4px 6px;
        font-size: 10px;
        vertical-align: top;
    }
    .detailed-first-table th {
        width: 32%;
        text-align: left;
        font-weight: 700;
    }
    .detailed-first-children-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-bottom: 10px;
    }
    .detailed-first-children-table th,
    .detailed-first-children-table td {
        border: 1px solid #000;
        padding: 3px 6px;
        font-size: 10px;
        vertical-align: top;
    }
    .detailed-first-children-table th {
        text-align: left;
        font-weight: 700;
    }
    .page-break-after { page-break-after: always; }

    .monitoring-label-value-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-bottom: 10px;
    }
    .monitoring-label-value-table th,
    .monitoring-label-value-table td {
        border: 1px solid #000;
        padding: 4px 6px;
        font-size: 10px;
        vertical-align: top;
    }
    .monitoring-label-value-table th {
        width: 32%;
        text-align: left;
        font-weight: 700;
        background: #f9fafb;
    }
    .monitoring-subheading {
        font-size: 12px;
        font-weight: 700;
        margin: 12px 0 6px;
        color: #111827;
    }
    .monitoring-children-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-bottom: 10px;
        font-size: 9px;
    }
    .monitoring-children-table th,
    .monitoring-children-table td {
        border: 1px solid #000;
        padding: 3px 4px;
        vertical-align: top;
    }
    .monitoring-children-table th {
        font-weight: 700;
        text-align: left;
        background: #f9fafb;
    }

    .close-file-main-title {
        text-align: center;
        font-size: 15px;
        font-weight: 700;
        margin: 6px 0 18px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .close-file-field-row { margin-bottom: 10px; font-size: 10px; line-height: 1.45; }
    .close-file-label { font-weight: 700; margin-right: 6px; }
    .close-file-dotted-value {
        border-bottom: 1px dotted #111827;
        min-width: 62%;
        display: inline-block;
        padding: 0 4px 1px;
        min-height: 14px;
        vertical-align: bottom;
    }
    .close-file-cnp-row { margin-top: 2px; }
    .close-file-cnp-cell {
        display: inline-block;
        width: 16px;
        height: 18px;
        border: 1px solid #000;
        text-align: center;
        line-height: 18px;
        font-size: 9px;
        margin-right: 3px;
        vertical-align: middle;
    }
    .close-file-subheading {
        font-size: 11px;
        font-weight: 700;
        margin: 14px 0 6px;
        color: #111827;
    }
    .close-file-bordered {
        border: 1px solid #000;
        padding: 8px 10px 10px;
        margin-bottom: 12px;
    }
    .close-file-checkbox-line { margin: 3px 0; font-size: 10px; }
    .close-file-checkbox-label { display: block; line-height: 1.35; }
    .close-file-checkbox-label input { margin-right: 8px; vertical-align: middle; }
    .close-file-inline-dots {
        border-bottom: 1px dotted #111827;
        min-width: 200px;
        display: inline-block;
        margin-left: 4px;
        padding: 0 6px 1px;
        vertical-align: bottom;
        min-height: 12px;
    }
    .close-file-extra-line { margin-top: 8px; font-size: 10px; }
    .close-file-label-inline { font-weight: 700; margin-right: 6px; }
    .close-file-situation-box {
        border: 1px solid #000;
        min-height: 110px;
        padding: 8px;
        font-size: 10px;
        margin-bottom: 14px;
        white-space: pre-wrap;
    }
    .close-file-date-row { margin-top: 18px; }
    .close-file-date-value { min-width: 100px; }
    .close-file-muted {
        font-size: 9px;
        color: #374151;
        margin: 6px 0 12px;
        font-style: italic;
    }
    .close-file-signature-page { page-break-before: always; padding-top: 28px; }
    .close-file-signature-line { font-size: 11px; margin: 0; line-height: 1.7; }
    .close-file-signature-name { font-weight: 600; margin: 0 6px; }
    .close-file-signature-dots {
        border-bottom: 1px dotted #111827;
        display: inline-block;
        min-width: 52%;
        margin-left: 6px;
        vertical-align: bottom;
        min-height: 14px;
    }

    .monthly-sheet-doc-title {
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        margin: 8px 0 16px;
        text-transform: uppercase;
    }
    .monthly-sheet-info-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-bottom: 14px;
        font-size: 10px;
    }
    .monthly-sheet-info-table th,
    .monthly-sheet-info-table td {
        border: 1px solid #000;
        padding: 5px 8px;
        vertical-align: top;
    }
    .monthly-sheet-info-table th {
        width: 32%;
        font-weight: 700;
        text-align: left;
        background: #f9fafb;
    }
    .monthly-sheet-section-title {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        margin: 14px 0 8px;
    }
    .monthly-sheet-subheading {
        font-size: 11px;
        font-weight: 700;
        margin: 14px 0 8px;
    }
    .monthly-sheet-data-table,
    .monthly-sheet-team-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-bottom: 12px;
        font-size: 9px;
    }
    .monthly-sheet-data-table th,
    .monthly-sheet-data-table td,
    .monthly-sheet-team-table th,
    .monthly-sheet-team-table td {
        border: 1px solid #000;
        padding: 3px 5px;
        vertical-align: top;
    }
    .monthly-sheet-data-table th,
    .monthly-sheet-team-table th {
        font-weight: 700;
        background: #f9fafb;
        text-align: left;
    }
    .monthly-sheet-empty {
        text-align: center;
        color: #6b7280;
        font-style: italic;
    }
    .monthly-sheet-page-break {
        page-break-after: always;
    }
    .monthly-sheet-gps-checks {
        margin-top: 6px;
    }
    .monthly-sheet-inline-cb {
        display: block;
        font-size: 9px;
        margin: 2px 0;
    }
    .monthly-sheet-inline-cb input {
        margin-right: 6px;
        vertical-align: middle;
    }
    .monthly-sheet-cb-cell {
        text-align: center;
    }

    .psych-sheet-title {
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        margin: 6px 0 14px;
    }
    .psych-sheet-subtitle {
        font-size: 11px;
        font-weight: 700;
        margin: 14px 0 8px;
        text-transform: uppercase;
    }
    .psych-sheet-meta-table,
    .psych-sheet-data-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-bottom: 12px;
        font-size: 9px;
    }
    .psych-sheet-meta-table th,
    .psych-sheet-meta-table td,
    .psych-sheet-data-table th,
    .psych-sheet-data-table td {
        border: 1px solid #000;
        padding: 4px 5px;
        vertical-align: top;
    }
    .psych-sheet-meta-table th,
    .psych-sheet-data-table th {
        font-weight: 700;
        text-align: left;
        background: #f9fafb;
    }
    .psych-sheet-box {
        border: 1px solid #000;
        min-height: 70px;
        padding: 6px;
        font-size: 9px;
        white-space: pre-wrap;
        margin-bottom: 10px;
    }
</style>
