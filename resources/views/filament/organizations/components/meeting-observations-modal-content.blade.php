<div class="space-y-4">
    @if(filled($meeting->topic))
        <div>
            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('intervention_plan.labels.topics_covered') }}</dt>
            <dd class="mt-1.5 text-sm text-gray-900 dark:text-white">{{ $meeting->topic }}</dd>
        </div>
    @endif
    <div>
        <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('intervention_plan.labels.observations') }}</dt>
        <dd class="mt-1.5 text-sm text-gray-900 dark:text-white prose prose-sm max-w-none dark:prose-invert">{!! $meeting->observations ?: '—' !!}</dd>
    </div>
</div>
