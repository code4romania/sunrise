@php($section = $section ?? [])
@if(trim((string) ($section['title'] ?? '')) !== '')
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
    @case('risk_factors_table')
        @include('exports.partials.sections.risk-factors', [
            'rows' => $section['rows'] ?? [],
            'extraRows' => $section['extraRows'] ?? [],
        ])
        @break
    @default
        @include('exports.partials.sections.generic-fields', ['rows' => $section['rows'] ?? []])
@endswitch
