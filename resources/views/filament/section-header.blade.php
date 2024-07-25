@props([
    'icon' => null,
    'message' => null,
    'actionUrl' => null,
    'actionLabel' => null,
    'bgColor' => 'primary',
])

<div @class(['class="fi-section-header flex flex-col gap-3 cursor-pointer px-6 py-4"',
        'bg-danger-200 text-danger-800' => $bgColor === 'danger',
	    'bg-warning-200 text-warning-800' => $bgColor === 'warning',
	    'bg-success-200 text-success-800' => $bgColor === 'success',
	    'bg-primary-50' => $bgColor === 'primary',
	    ])>
    <div class="flex items-center gap-3">
        <div class="grid flex-1 gap-y-1")>
            @if ($icon)
                <x-dynamic-component :component="$icon" class="w-5 h-5 shrink-0 text-primary-600"/>
            @endif
            <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                {{ $message }}
            </h3>

        </div>

        <div>
            @if ($actionUrl && $actionLabel)
                <a href="{{ $actionUrl }}"
                   class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-md fi-link-size-md gap-1.5 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action {{$actionAlign}}">

                    <span
                        class="text-sm underline group-hover/link:no-underline group-focus-visible/link:no-underline text-custom-600 dark:text-custom-400"
                        style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                        {{ $actionLabel }}
                    </span>
                </a>
            @endif
        </div>

    </div>





</div>

