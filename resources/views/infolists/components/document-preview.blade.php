@php
    $file = $getFile();
@endphp

@if($file)
    <div class="fi-in-document-preview">
        @if($file->mime_type === 'application/pdf' || str_starts_with($file->mime_type, 'image/'))
            <iframe
                src="{{ $file->getUrl() }}"
                class="w-full h-[600px] border rounded-lg"
                frameborder="0"
            ></iframe>
        @else
            <div class="flex items-center justify-center p-8 border rounded-lg bg-gray-50 dark:bg-gray-900">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $file->name }}</p>
                    <a
                        href="{{ $file->getUrl() }}"
                        target="_blank"
                        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        {{ __('general.action.download') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
@else
    <div class="fi-in-document-preview-empty">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('general.labels.no_document') }}</p>
    </div>
@endif
