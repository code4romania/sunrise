@php
    $meetings = $record->meetings()
        ->with(['specialist.user', 'specialist.roleForDisplay'])
        ->orderByDesc('id')
        ->get();
@endphp

@if ($meetings->isEmpty())
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('intervention_plan.labels.empty_meetings_list') }}</p>
@else
    <div class="space-y-6">
        @foreach ($meetings as $index => $meeting)
            @php
                $number = $meetings->count() - $index;
                $status = $meeting->status;
                $statusLabel = $status?->getLabel() ?? '—';
                $statusColor = $status === \App\Enums\MeetingStatus::REALIZED ? 'success' : 'warning';
                $dateFormatted = $meeting->date?->translatedFormat('d.m.Y') ?? '—';
                $timeFormatted = $meeting->time ? (\Carbon\Carbon::parse($meeting->time)->format('H:i')) : '';
                $dateTimeLabel = $status === \App\Enums\MeetingStatus::REALIZED
                    ? __('intervention_plan.labels.session_date')
                    : __('intervention_plan.labels.date_and_time');
                $dateTimeValue = $timeFormatted ? "{$dateFormatted}, {$timeFormatted}" : $dateFormatted;
                $specialistName = $meeting->specialist?->name_role ?? '—';
                $durationValue = $meeting->duration !== null ? $meeting->duration . ' min' : '—';
            @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex min-w-0 flex-1 flex-wrap items-center gap-3">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ __('intervention_plan.headings.meeting_repeater', ['number' => $number]) }}
                        </h3>
                        <span class="inline-flex items-center rounded-md px-3 py-1 text-sm font-medium
                            @if ($statusColor === 'success')
                                bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300
                            @else
                                bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300
                            @endif">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <button
                        type="button"
                        wire:click="mountAction('edit_meeting', arguments: ['meeting' => {{ $meeting->id }}])"
                        class="inline-flex shrink-0 items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400"
                    >
                        <x-filament::icon icon="heroicon-o-pencil-square" class="h-4 w-4" />
                        {{ __('general.action.edit') }}
                    </button>
                </div>
                <div class="grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-3">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $dateTimeLabel }}</dt>
                        <dd class="mt-1.5 text-sm text-gray-900 dark:text-white">{{ $dateTimeValue }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('intervention_plan.labels.duration') }}</dt>
                        <dd class="mt-1.5 text-sm text-gray-900 dark:text-white">{{ $durationValue }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('intervention_plan.labels.responsible_specialist') }}</dt>
                        <dd class="mt-1.5 text-sm text-gray-900 dark:text-white">{{ $specialistName }}</dd>
                    </div>
                </div>
                <div class="mt-6 border-t border-gray-100 pt-6 dark:border-gray-700">
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('intervention_plan.labels.topics_covered') }}</dt>
                    <dd class="mt-1.5 text-sm leading-relaxed text-gray-900 dark:text-white">
                        @php
                            $topicsRaw = $meeting->topic ?? '';
                            $topicsList = $topicsRaw ? array_filter(array_map('trim', preg_split('/[;,]/', $topicsRaw))) : [];
                        @endphp
                        @if (count($topicsList) > 0)
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($topicsList as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $topicsRaw ?: '—' }}
                        @endif
                    </dd>
                </div>
                <div class="mt-5">
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('intervention_plan.labels.observations') }}</dt>
                    <dd class="mt-1.5 text-sm leading-relaxed text-gray-900 dark:text-white prose prose-sm max-w-none dark:prose-invert">{!! $meeting->observations ?: '—' !!}</dd>
                </div>
            </div>
        @endforeach
    </div>
@endif
