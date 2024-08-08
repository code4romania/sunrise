@props(['actions', 'alignment' => null, 'fullWidth' => false])
@php
    $visibleActions = false;
    foreach($actions as $action)
    {
        if ($action->isVisible())
        {
            $visibleActions = true;
        }
    }
@endphp
@if ($visibleActions)
    <div @class([
        'fi-form-actions',
        'fi-sticky sticky bottom-0 -mx-4 transform bg-white p-4 shadow-lg ring-1 ring-gray-950/5 transition dark:bg-gray-900 dark:ring-white/10 md:bottom-4 md:rounded-xl' => $this->areFormActionsSticky(),
    ])>
        <x-filament::actions
            :actions="$actions"
            :alignment="$alignment ?? $this->getFormActionsAlignment()"
            :full-width="$fullWidth" />
    </div>
@endif
