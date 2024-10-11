<x-filament-panels::page class="fi-dashboard-page">
    <div>
        <form wire:submit.prevent="submit" class="space-y-4">
            {{ $this->form }}

            <div >
                <x-filament::button type="submit" class="float-right">
                    {{ __('report.actions.generate') }}
                </x-filament::button>
            </div>
        </form>
    </div>
    <div>
        <div>
            @if ($this->report_type)
                {{ $this->infolist }}
            @endif
        </div>

    </div>
</x-filament-panels::page>
