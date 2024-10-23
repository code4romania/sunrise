<x-filament-widgets::widget>
    <x-filament::section>
        {{ $this->form }}

        <div class="pt-4">
            @foreach($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>

    </x-filament::section>

    @if($this->getInfolistSchema())
        <x-filament::section class="pt-4">
            {{ $this->getWidgetInfolist() }}
        </x-filament::section>
    @endif

</x-filament-widgets::widget>
