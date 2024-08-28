<x-filament-panels::page class="fi-dashboard-page">
    <div>
        <form wire:submit.prevent="submit" class="space-y-4">
            {{ $this->form }}

            <x-filament::button type="submit">
                Trimite
            </x-filament::button>
        </form>
{{--        {{ $this->form }}--}}
{{--        {{ $this->table }}--}}
    </div>
</x-filament-panels::page>
