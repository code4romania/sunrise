<x-filament-panels::page.simple>
    <form wire:submit="handle">
        {{ $this->form }}

        <div class="fi-form-actions">
            @foreach($this->getCachedFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>
</x-filament-panels::page.simple>
