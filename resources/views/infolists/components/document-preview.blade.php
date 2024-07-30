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
{{--                TODO add empty state--}}
{{--                <x-tables::empty-state--}}
{{--                    icon="icon-empty-state"--}}
{{--                    :heading="__('document.empty_preview.title')"--}}
{{--                    :description="__('document.empty_preview.description')" />--}}
        @endswitch
    @endif

</div>
