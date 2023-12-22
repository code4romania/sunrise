<div {{ $attributes->merge($getExtraAttributes(), escape: false)->class('flex flex-wrap gap-2') }}>
    @foreach ($getServices() as $service)
        <x-chip
            :size="$getSize($state)"
            :color="$service->is_available ? 'primary' : 'gray'"
            :disabled="!$service->is_available">
            {{ $service->service->name }}
        </x-chip>
    @endforeach
</div>
