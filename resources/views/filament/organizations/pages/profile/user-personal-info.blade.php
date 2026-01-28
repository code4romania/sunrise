<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="fi-form-actions">
            @foreach($this->getCachedFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>
</x-filament-panels::page>
