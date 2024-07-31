<div
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class([]) }}>

    @php
        $file = $getFile();
    @endphp

    @if ($file)
        @switch($file->type)
            @case('image')
                <img
                    src="{{ $file->getFullUrl() }}"
                    alt="{{ $file->name }}"
                    class="w-full" />
                @break

            @case('pdf')
                <iframe
                    src="{{ $getFile()->getFullUrl() }}"
                    title="{{ $file->name }}"
                    class="w-full h-screen">
                </iframe>
                @break

            @default
                <x-filament-tables::empty-state
                    icon="heroicon-o-eye-slash"
                    :heading="__('beneficiary.section.documents.labels.empty_state_header')"
                    :description="__('beneficiary.section.documents.labels.empty_state_description')" />
        @endswitch
    @endif

</div>
