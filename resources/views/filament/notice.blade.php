@props([
    'icon' => null,
    'message' => null,
    'actionUrl' => null,
    'actionLabel' => null,
    'bgClass' => 'bg-primary-50',
])

<div class="flex items-center gap-3 p-4 -mx-6 -mt-6 rounded-xl {{ $bgClass }}">
    @if ($icon)
        <x-dynamic-component :component="$icon" class="w-5 h-5 shrink-0 text-primary-600" />
    @endif

    <div class="flex-1 text-sm">
        {{ $message }}

        @if ($actionUrl && $actionLabel)
            <a href="{{ $actionUrl }}"
                class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-md fi-link-size-md gap-1.5 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action">

                <span
                    class="text-sm underline group-hover/link:no-underline group-focus-visible/link:no-underline text-custom-600 dark:text-custom-400"
                    style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                    {{ $actionLabel }}
                </span>
            </a>
        @endif
    </div>

</div>

<style>
    .bg-danger {
        --c-200:var(--danger-200);
        --c-800:var(--danger-800);
    }

    .bg-warning {
        --c-200:var(--warning-200);
        --c-800:var(--warning-800);
    }

    .bg-success {
        --c-200:var(--success-200);
        --c-800:var(--success-800);"
    }
</style>
