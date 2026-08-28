@php($section = $section ?? [])
@php($sectionTitle = trim((string) ($section['title'] ?? '')))
@php($pageTitle = trim((string) ($reportTitle ?? '')))
@php($showSectionTitle = $sectionTitle !== '' && mb_strtolower($sectionTitle) !== mb_strtolower($pageTitle))
@if($showSectionTitle)
    <div class="section-title">{{ $section['title'] }}</div>
@endif
@switch($section['type'] ?? null)
    @case('detailed_first_page')
        @include('exports.partials.sections.detailed-first-page', ['data' => $section['data'] ?? []])
        @break
    @case('initial_evaluation_identity_beneficiary')
        @include('exports.partials.sections.initial-evaluation-identity', ['identity' => $section['identity'] ?? []])
        @break
    @case('initial_evaluation_children_table')
        @include('exports.partials.sections.initial-evaluation-children', ['children' => $section['children'] ?? []])
        @break
    @case('detailed_specialists_table')
        @include('exports.partials.sections.detailed-specialists-table', ['rows' => $section['rows'] ?? []])
        @break
    @case('detailed_meetings_table')
        @include('exports.partials.sections.detailed-meetings-table', ['rows' => $section['rows'] ?? []])
        @break
    @case('detailed_violence_history_table')
        @include('exports.partials.sections.detailed-violence-history-table', ['rows' => $section['rows'] ?? []])
        @break
    @case('detailed_multidisciplinary_section')
        @include('exports.partials.sections.detailed-multidisciplinary-section', ['data' => $section['data'] ?? []])
        @break
    @case('monitoring_label_value_table')
        @include('exports.partials.sections.monitoring-label-value-table', ['rows' => $section['rows'] ?? []])
        @break
    @case('monitoring_children_table')
        @include('exports.partials.sections.monitoring-children-table', ['rows' => $section['rows'] ?? []])
        @break
    @case('monitoring_general_grouped')
        @include('exports.partials.sections.monitoring-general-grouped', [
            'topRows' => $section['topRows'] ?? [],
            'measureBlocks' => $section['measureBlocks'] ?? [],
        ])
        @break
    @case('close_file_form')
        @include('exports.partials.sections.close-file-form', ['data' => $section['data'] ?? []])
        @break
    @case('monthly_plan_sheet')
        @include('exports.partials.sections.monthly-plan-sheet', ['data' => $section['data'] ?? []])
        @break
    @case('psychological_counseling_sheet')
        @include('exports.partials.sections.psychological-counseling-sheet', ['data' => $section['data'] ?? []])
        @break
    @case('legal_counseling_sheet')
        @include('exports.partials.sections.legal-counseling-sheet', ['data' => $section['data'] ?? []])
        @break
    @case('social_counseling_sheet')
        @include('exports.partials.sections.social-counseling-sheet', ['data' => $section['data'] ?? []])
        @break
    @case('risk_factors_table')
        @include('exports.partials.sections.risk-factors', [
            'rows' => $section['rows'] ?? [],
            'extraRows' => $section['extraRows'] ?? [],
        ])
        @break
    @default
        @include('exports.partials.sections.generic-fields', ['rows' => $section['rows'] ?? []])
@endswitch
