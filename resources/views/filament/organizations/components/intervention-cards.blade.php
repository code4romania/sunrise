@php
    use App\Filament\Organizations\Resources\Cases\CaseResource;

    $service = $record;
    $beneficiary = $service->interventionPlan?->beneficiary;
    $interventions = $service->beneficiaryInterventions()
        ->with(['organizationServiceIntervention.serviceInterventionWithoutStatusCondition', 'specialist.user', 'specialist.roleForDisplay'])
        ->orderByDesc('id')
        ->get();
@endphp

@if ($interventions->isEmpty())
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('intervention_plan.headings.empty_state_service_intervention_table') }}</p>
@else
    <div class="space-y-4">
        @foreach ($interventions as $intervention)
            @php
                $typeName = $intervention->organizationServiceIntervention?->serviceInterventionWithoutStatusCondition?->name ?? '—';
                $specialistName = $intervention->specialist?->name_role ?? '—';
                $interval = $intervention->interval ?: '—';
                $objections = $intervention->objections ?? '';
                $detailsUrl = $beneficiary ? CaseResource::getUrl('view_beneficiary_intervention', [
                    'record' => $beneficiary,
                    'interventionService' => $service,
                    'beneficiaryIntervention' => $intervention->getKey(),
                ]) : '#';
            @endphp
            {{-- Card aligned with Sunrise Figma: left accent, clear sections, title + notes --}}
            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="absolute left-0 top-0 h-full w-1 bg-primary-500 dark:bg-primary-400" aria-hidden="true"></div>
                <div class="p-5 pl-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-semibold leading-tight text-gray-900 dark:text-white">
                                {{ $typeName }}
                            </h3>
                        </div>
                        <a href="{{ $detailsUrl }}"
                           class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-primary-600 bg-primary-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm transition hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-primary-500 dark:bg-primary-500 dark:hover:bg-primary-600">
                            <x-filament::icon icon="heroicon-o-eye" class="h-4 w-4" />
                            {{ __('intervention_plan.actions.view_details') }}
                        </a>
                    </div>
                    <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('intervention_plan.labels.responsible_person') }}
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $specialistName }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('intervention_plan.labels.period_of_provision') }}
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $interval }}</dd>
                        </div>
                    </dl>
                    <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-700">
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.observations') }}
                        </dt>
                        <dd class="mt-1 whitespace-pre-wrap text-sm leading-relaxed text-gray-900 dark:text-white">
                            {{ $objections !== '' ? $objections : '—' }}
                        </dd>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
