<x-filament-panels::page class="fi-dashboard-page">
    <div>
        <form wire:submit.prevent="submit" class="space-y-4">
            {{ $this->form }}

            <div>
                <x-filament::button type="submit" class="float-right">
                    {{ __('report.actions.generate') }}
                </x-filament::button>
            </div>
        </form>
    </div>

    @if ($this->report_feature)
        <div class="space-y-4">
            @if (in_array($this->report_feature, ['37', '38'], true))
                <x-filament::section>
                    {{ __('report.new.separate_note') }}
                </x-filament::section>
            @endif

            {{ $this->infolist }}
        </div>
    @endif
</x-filament-panels::page>
