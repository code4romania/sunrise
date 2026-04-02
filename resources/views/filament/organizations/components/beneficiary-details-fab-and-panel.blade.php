@php
    /** @var \App\Models\Beneficiary $beneficiary */
    /** @var bool $panelOpen */
    /** @var string $viewCaseUrl */
@endphp

<div class="pointer-events-none">
    @if (! $panelOpen)
        <div
            class="fi-beneficiary-details-fab pointer-events-auto fixed right-6 z-40"
            x-data="{
                bottomPx: 24,
                init() {
                    const update = () => {
                        const bar = document.querySelector('.fi-sc-actions.fi-sticky .fi-ac');
                        this.bottomPx = bar ? Math.ceil(bar.getBoundingClientRect().height) + 16 : 24;
                    };
                    update();
                    const ro = new ResizeObserver(() => update());
                    const wrap = document.querySelector('.fi-sc-actions');
                    if (wrap) {
                        ro.observe(wrap);
                    }
                    window.addEventListener('resize', update);
                },
            }"
            x-bind:style="'bottom: ' + bottomPx + 'px'"
            wire:key="beneficiary-details-fab-{{ $beneficiary->getKey() }}"
        >
            <button
                type="button"
                wire:click="openBeneficiaryDetailsPanel"
                class="fi-btn relative inline-grid min-w-0 transform-cpu cursor-pointer grid-flow-col items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold outline-none transition duration-75 focus-visible:ring-2 fi-btn-size-md fi-btn-color-primary bg-primary-600 text-white shadow-lg hover:bg-primary-500 focus-visible:ring-primary-500 dark:bg-primary-500 dark:hover:bg-primary-400"
                aria-expanded="false"
                aria-controls="beneficiary-details-side-panel"
                aria-label="{{ __('case.view.identity_page.fab_beneficiary_details') }}"
            >
                <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5 shrink-0" />
                <span class="max-w-[12rem] truncate sm:max-w-none">{{ __('case.view.identity_page.fab_beneficiary_details') }}</span>
            </button>
        </div>
    @endif

    @if ($panelOpen)
        {{-- Shell: overflow-visible so the collapse handle is never clipped at the left edge --}}
        <aside
            id="beneficiary-details-side-panel"
            wire:key="beneficiary-details-panel-{{ $beneficiary->getKey() }}"
            class="pointer-events-auto fixed right-0 top-0 z-50 flex h-screen w-[min(100vw,22rem)] flex-col overflow-visible border-l border-gray-200 bg-white shadow-2xl ring-1 ring-gray-950/5 dark:border-white/10 dark:bg-gray-900 dark:ring-white/10 sm:w-[min(100vw,26rem)]"
            role="complementary"
            aria-label="{{ __('case.view.identity_page.fab_beneficiary_details') }}"
        >
            {{-- Handle: centered on left edge (half in / half out), narrow & tall tab --}}
            <button
                type="button"
                wire:click="closeBeneficiaryDetailsPanel"
                class="absolute -left-4 py-8 bottom-1.5 z-[60] flex h-12 w-8 -translate-x-1/2 -translate-y-1/2 cursor-pointer items-center justify-center rounded-md border border-purple-100 bg-purple-50 shadow-sm outline-none transition-colors hover:bg-purple-100/90 focus-visible:ring-2 focus-visible:ring-purple-400/60 dark:border-purple-900/60 dark:bg-purple-950/50 dark:text-purple-300 dark:shadow-none dark:hover:bg-purple-900/60 dark:focus-visible:ring-purple-500/50"
                aria-label="{{ __('general.action.close') }}"
            >
                <x-filament::icon
                    icon="heroicon-o-chevron-right"
                    class="h-5 w-5 shrink-0 text-purple-500 dark:text-purple-400"
                />
            </button>

            {{-- Inner column: clipping only for scroll regions, not the handle --}}
            <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden">
                <div class="shrink-0 border-b border-gray-200 px-3 py-3 dark:border-white/10">
                    <h2 class="truncate pr-1 text-base font-semibold text-gray-950 dark:text-white">
                        {{ __('case.view.identity_page.fab_beneficiary_details') }}
                    </h2>
                </div>

                <div class="flex min-h-0 flex-1 flex-col overflow-hidden">
                    <div class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden px-3 py-3">
                        <livewire:organizations.beneficiary-details-panel-infolist
                            :beneficiary-id="$beneficiary->getKey()"
                            :wire:key="'bd-infolist-'.$beneficiary->getKey()"
                        />
                    </div>

                    <div class="shrink-0 border-t border-gray-200 px-3 py-3 dark:border-white/10">
                        <a
                            href="{{ $viewCaseUrl }}"
                            wire:navigate
                            class="fi-btn relative inline-grid w-full min-w-0 transform-cpu cursor-pointer grid-flow-col items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold outline-none transition duration-75 focus-visible:ring-2 fi-btn-color-gray fi-btn-size-md ring-1 ring-gray-950/10 hover:bg-gray-50 dark:ring-white/20 dark:hover:bg-white/5"
                        >
                            {{ __('case.view.view_full_beneficiary') }}
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    @endif
</div>
