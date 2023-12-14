@php
    $services = $getState()->filter(fn($service) => $service->is_visible);
@endphp

<div {{ $attributes->merge($getExtraAttributes(), escape: false)->class('flex flex-wrap gap-2') }}>
    @foreach ($services as $service)
        <x-chip
            :size="$getSize($state)"
            :color="$service->is_available ? 'primary' : 'gray'"
            :disabled="!$service->is_available">
            {{ $service->service->name }}
        </x-chip>
    @endforeach
</div>
