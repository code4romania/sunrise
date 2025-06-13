<aside
    class="flex flex-col items-start gap-3 border-t pt-7"
    x-show="$store.sidebar.isOpen"
    x-transition:enter="lg:transition lg:delay-100"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100">

    <h2 class="flex-1 text-sm font-medium leading-6 text-gray-500 fi-sidebar-group-label dark:text-gray-400">
        @lang('navigation.developed_by')
    </h2>

    <img src="{{ Vite::image('eeirh.png') }}" class="h-10" alt="">

    <img src="{{ Vite::image('code4romania.png') }}" class="h-8" alt="">
</aside>
