<div class="fi-fab-ctn fixed bottom-6 right-6 z-30">
    <a
        href="{{ $beneficiaryDetailsUrl }}"
        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-xl shadow-lg fi-btn-color-primary fi-btn-size-md flex shrink-0 cursor-pointer gap-2 px-4 py-3 inline-grid bg-primary-500 hover:bg-primary-600 text-white dark:bg-primary-500 dark:hover:bg-primary-600"
    >
        <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5" />
        <span>{{ __('case.view.identity_page.fab_beneficiary_details') }}</span>
    </a>
</div>
