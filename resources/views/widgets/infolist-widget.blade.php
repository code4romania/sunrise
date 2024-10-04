<x-filament-widgets::widget>
    <div class="text-center pb-4">
        @foreach($this->getHeaderActions() as $action)
            {{ $action }}
        @endforeach
    </div>

    <x-filament::section>
        {{ $this->getWidgetInfolist() }}
    </x-filament::section>

    <x-filament-actions::modals />
        @if (filled($this->defaultAction))
            <div
                wire:init="mountAction(@js($this->defaultAction) @if (filled($this->defaultActionArguments)) , @js($this->defaultActionArguments) @endif)"
            ></div>
        @endif
</x-filament-widgets::widget>
