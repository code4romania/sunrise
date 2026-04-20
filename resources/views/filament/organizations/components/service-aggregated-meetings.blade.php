@php
    use App\Models\InterventionMeeting;

    $meetingsTable = (new InterventionMeeting)->getTable();
    $meetings = $record->meetings()
        ->with([
            'beneficiaryIntervention.organizationServiceIntervention.serviceInterventionWithoutStatusCondition',
            'specialist.user',
            'specialist.roleForDisplay',
        ])
        ->orderByDesc("{$meetingsTable}.date")
        ->orderByDesc("{$meetingsTable}.id")
        ->get();
@endphp

<p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
    {{ __('intervention_plan.headings.service_meetings_centralized_hint') }}
</p>

<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                {{ __('intervention_plan.headings.service_meetings_table') }}
            </h3>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                {{ __('intervention_plan.headings.meetings_count_sessions', ['count' => $meetings->count()]) }}
            </p>
        </div>
    </div>

    @if ($meetings->isEmpty())
        <div class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('intervention_plan.labels.empty_meetings_list') }}
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.intervention') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.meet_number') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.status') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.date') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.time') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.duration') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('intervention_plan.labels.specialist') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <span class="sr-only">{{ __('general.action.edit') }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <span class="sr-only">{{ __('general.action.view_observations') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @foreach ($meetings as $index => $meeting)
                        @php
                            $number = $meetings->count() - $index;
                            $interventionLabel = $meeting->beneficiaryIntervention?->organizationServiceIntervention?->serviceInterventionWithoutStatusCondition?->name ?? '—';
                            $statusLabel = $meeting->status?->getLabel() ?? '—';
                            $dateFormatted = $meeting->date?->translatedFormat('d/m/Y') ?? '—';
                            $timeFormatted = $meeting->time ? \Carbon\Carbon::parse($meeting->time)->format('H:i') : '—';
                            $durationValue = $meeting->duration !== null ? $meeting->duration.' min' : '—';
                            $specialistName = $meeting->specialist?->name_role ?? '—';
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $interventionLabel }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $number }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $statusLabel }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $dateFormatted }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $timeFormatted }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $durationValue }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $specialistName }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <button
                                    type="button"
                                    wire:click="mountAction('edit_meeting', @js(['meeting' => $meeting->getKey()]))"
                                    class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400"
                                >
                                    {{ __('general.action.edit') }}
                                </button>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <button
                                    type="button"
                                    wire:click="mountAction('view_meeting_observations', @js(['meeting' => $meeting->getKey()]))"
                                    class="font-medium text-primary-600 underline hover:text-primary-500 dark:text-primary-400"
                                >
                                    {{ __('general.action.view_observations') }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
