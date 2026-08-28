@php($d = $data ?? [])
@php($benefitRows = $d['benefit_rows'] ?? [])
@php($serviceRows = $d['service_rows'] ?? [])
@php($interventionRows = $d['intervention_rows'] ?? [])
@php($teamRows = $d['team_rows'] ?? [])

<div class="monthly-sheet-doc-title">{{ __('intervention_plan.sheet.document_title') }}</div>

<table class="monthly-sheet-info-table">
    <tr>
        <th>{{ __('intervention_plan.sheet.beneficiary_name') }}</th>
        <td>{{ $d['beneficiary_name'] ?? '—' }}</td>
    </tr>
    <tr>
        <th>{{ __('intervention_plan.sheet.sheet_date') }}</th>
        <td>{{ $d['sheet_date'] ?? '—' }}</td>
    </tr>
    <tr>
        <th>{{ __('intervention_plan.sheet.plan_period') }}</th>
        <td>{{ $d['plan_period'] ?? '—' }}</td>
    </tr>
</table>

@if(count($benefitRows) > 0)
<div class="monthly-sheet-section-title">{{ __('intervention_plan.sheet.social_benefits') }}</div>
<table class="monthly-sheet-data-table">
    <thead>
    <tr>
        <th style="width: 6%;">{{ __('intervention_plan.sheet.nr_crt_short') }}</th>
        <th style="width: 18%;">{{ __('intervention_plan.sheet.type') }}</th>
        <th style="width: 14%;">{{ __('intervention_plan.sheet.amount') }}</th>
        <th style="width: 22%;">{{ __('intervention_plan.sheet.local_authority') }}</th>
        <th style="width: 12%;">{{ __('intervention_plan.sheet.benefit_start') }}</th>
        <th style="width: 28%;">{{ __('intervention_plan.sheet.benefit_period') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($benefitRows as $row)
        <tr>
            <td>{{ $row['nr'] ?? '' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['type'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['amount'] ?? '—' }}</td>
            <td>{{ $row['authority'] ?? '—' }}</td>
            <td>{{ $row['start'] ?? '—' }}</td>
            <td>{{ $row['period'] ?? '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

@if(count($serviceRows) > 0)
<div class="monthly-sheet-section-title">{{ __('intervention_plan.sheet.social_services') }}</div>
<table class="monthly-sheet-data-table">
    <thead>
    <tr>
        <th style="width: 5%;">{{ __('intervention_plan.sheet.nr_crt_short') }}</th>
        <th style="width: 20%;">{{ __('intervention_plan.sheet.service_type_offered') }}</th>
        <th style="width: 14%;">{{ __('intervention_plan.sheet.responsible_institution') }}</th>
        <th style="width: 20%;">{{ __('intervention_plan.sheet.specific_objectives') }}</th>
        <th style="width: 10%;">{{ __('intervention_plan.sheet.service_start') }}</th>
        <th style="width: 13%;">{{ __('intervention_plan.sheet.service_period') }}</th>
        <th style="width: 18%;">{{ __('intervention_plan.sheet.responsible_persons') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($serviceRows as $i => $row)
        <tr>
            <td>{{ $i + 1 }}.</td>
            <td style="white-space: pre-wrap;">
                {{ $row['label'] ?? '—' }}
                @if(! empty($row['is_gps']))
                    <div class="monthly-sheet-gps-checks">
                        <label class="monthly-sheet-inline-cb">
                            <input type="checkbox" disabled {{ ! empty($row['admission_checked']) ? 'checked' : '' }}>
                            {{ __('intervention_plan.sheet.admission') }}
                        </label>
                        <label class="monthly-sheet-inline-cb">
                            <input type="checkbox" disabled {{ ! empty($row['continuation_checked']) ? 'checked' : '' }}>
                            {{ __('intervention_plan.sheet.continuation') }}
                        </label>
                    </div>
                @endif
            </td>
            <td style="white-space: pre-wrap;">{{ $row['institution'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['objectives'] ?? '—' }}</td>
            <td>{{ $row['start'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['period'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['responsible'] ?? '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

@if(count($interventionRows) > 0)
<div class="monthly-sheet-page-break"></div>
<div class="monthly-sheet-section-title">{{ __('intervention_plan.sheet.interventions_title') }}</div>
<table class="monthly-sheet-data-table">
    <thead>
    <tr>
        <th style="width: 5%;">{{ __('intervention_plan.sheet.nr_crt_short') }}</th>
        <th style="width: 28%;">{{ __('intervention_plan.sheet.intervention_service_type') }}</th>
        <th style="width: 37%;">{{ __('intervention_plan.sheet.objectives') }}</th>
        <th style="width: 30%;">{{ __('intervention_plan.sheet.observations') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($interventionRows as $i => $row)
        <tr>
            <td>{{ $i + 1 }}.</td>
            <td style="white-space: pre-wrap;">{{ $row['label'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['objectives'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['observations'] ?? '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

@if(count($teamRows) > 0)
<div class="monthly-sheet-subheading">{{ __('intervention_plan.sheet.multidisciplinary_team') }}</div>
<table class="monthly-sheet-team-table">
    <thead>
    <tr>
        <th style="width: 40%;">{{ __('intervention_plan.sheet.name_surname') }}</th>
        <th style="width: 12%;">{{ __('intervention_plan.sheet.no') }}</th>
        <th style="width: 12%;">{{ __('intervention_plan.sheet.yes') }}</th>
        <th style="width: 36%;">{{ __('intervention_plan.sheet.signature') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($teamRows as $row)
        <tr>
            <td>{{ ($row['role'] ?? '—').' — '.($row['name'] ?? '—') }}</td>
            <td class="monthly-sheet-cb-cell"><input type="checkbox" disabled></td>
            <td class="monthly-sheet-cb-cell"><input type="checkbox" disabled></td>
            <td style="height: 22px;"></td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

<div class="monthly-sheet-subheading">{{ __('intervention_plan.sheet.beneficiary_block') }}</div>
<table class="monthly-sheet-team-table">
    <thead>
    <tr>
        <th style="width: 50%;">{{ __('intervention_plan.sheet.name_surname') }}</th>
        <th style="width: 50%;">{{ __('intervention_plan.sheet.signature') }}</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $d['beneficiary_sign_name'] ?? '—' }}</td>
        <td style="height: 28px;"></td>
    </tr>
    </tbody>
</table>
