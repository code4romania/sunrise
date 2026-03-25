@php
    use App\Models\MonthlyPlanService;

    /** @var \App\Models\MonthlyPlan|null $record */
    $plan = $record;
    $services = $plan instanceof \App\Models\MonthlyPlan
        ? $plan->monthlyPlanServices->sortBy(fn (MonthlyPlanService $row) => $row->service?->sort ?? $row->id)
        : collect();

    $formatDate = static function (mixed $value): string {
        if ($value === null || $value === '' || $value === '-') {
            return '—';
        }
        try {
            return \Carbon\Carbon::parse($value)->format('d.m.Y');
        } catch (\Throwable) {
            return '—';
        }
    };

    $displayText = static function (?string $value): string {
        $trimmed = trim((string) ($value ?? ''));

        return $trimmed !== '' ? $trimmed : '—';
    };

    $interventionObjectives = static function (\App\Models\MonthlyPlanInterventions $intervention): string {
        $parts = array_filter([
            $intervention->objections,
            $intervention->expected_results,
            $intervention->procedure,
            $intervention->indicators,
            $intervention->achievement_degree,
        ], static fn (?string $p): bool => $p !== null && trim($p) !== '');

        if ($parts === []) {
            return '—';
        }

        return implode("\n\n", $parts);
    };
@endphp

@if ($services->isEmpty())
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('intervention_plan.headings.empty_state_service_table') }}</p>
@else
    <div class="w-full space-y-12">
        @foreach ($services as $serviceRow)
            @php
                $serviceName = $serviceRow->service?->name;
                $title = $serviceName
                    ? __('intervention_plan.labels.monthly_plan_service_block_title', ['name' => $serviceName])
                    : '—';
            @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>

                <dl class="mt-6 grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.responsible_institution') }}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $displayText($serviceRow->institution) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.responsible_persons') }}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $displayText($serviceRow->responsible_person) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.monthly_plan_service_interval_start') }}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $formatDate($serviceRow->start_date) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.monthly_plan_service_interval_end') }}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $formatDate($serviceRow->end_date) }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-8 border-t border-gray-100 pt-6 dark:border-gray-700">
                    <h4 class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('intervention_plan.labels.service_objective') }}
                    </h4>
                    <div class="mt-2 whitespace-pre-wrap text-sm leading-relaxed text-gray-900 dark:text-gray-100">
                        {{ $displayText($serviceRow->objective) }}
                    </div>
                </div>

                <div class="mt-8 overflow-x-auto">
                    <table class="w-full border-collapse overflow-hidden rounded-lg border border-gray-200 text-left text-sm dark:border-gray-600">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <th scope="col"
                                    class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800 dark:border-gray-600 dark:text-gray-100">
                                    {{ __('intervention_plan.headings.interventions') }}
                                </th>
                                <th scope="col"
                                    class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800 dark:border-gray-600 dark:text-gray-100">
                                    {{ __('intervention_plan.labels.objectives_short') }}
                                </th>
                                <th scope="col"
                                    class="border-b border-gray-200 px-4 py-3 font-semibold text-gray-800 dark:border-gray-600 dark:text-gray-100">
                                    {{ __('intervention_plan.labels.observations_column') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($serviceRow->monthlyPlanInterventions as $intervention)
                                <tr class="bg-white dark:bg-gray-900/30">
                                    <td
                                        class="border border-gray-200 px-4 py-3 align-top text-gray-900 dark:border-gray-600 dark:text-gray-100">
                                        {{ $intervention->serviceIntervention?->name ?? '—' }}
                                    </td>
                                    <td
                                        class="border border-gray-200 px-4 py-3 align-top whitespace-pre-wrap text-gray-900 dark:border-gray-600 dark:text-gray-100">
                                        {{ $interventionObjectives($intervention) }}
                                    </td>
                                    <td
                                        class="border border-gray-200 px-4 py-3 align-top whitespace-pre-wrap text-gray-900 dark:border-gray-600 dark:text-gray-100">
                                        {{ $displayText($intervention->observations) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"
                                        class="border border-gray-200 px-4 py-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                                        {{ __('intervention_plan.headings.empty_state_service_intervention_table') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 border-t border-gray-100 pt-6 dark:border-gray-700">
                    <h4 class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('intervention_plan.labels.service_details_label') }}
                    </h4>
                    <div class="mt-2 whitespace-pre-wrap text-sm leading-relaxed text-gray-900 dark:text-gray-100">
                        {{ $displayText($serviceRow->service_details) }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
