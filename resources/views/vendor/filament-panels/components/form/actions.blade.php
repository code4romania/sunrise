@props(['actions', 'alignment' => null, 'fullWidth' => false])

@if (count($actions))
    <div @class([
        'fi-form-actions',
        'fi-sticky sticky bottom-0 -mx-4 bg-white p-4 shadow-lg ring-1 ring-gray-950/5 md:bottom-4 md:rounded-xl' =>
            $this->areFormActionsSticky() && auth()->check(),
    ])>
        <x-filament::actions
            :actions="$actions"
            :alignment="$alignment ?? $this->getFormActionsAlignment()"
            :full-width="$fullWidth" />
    </div>
@endif
