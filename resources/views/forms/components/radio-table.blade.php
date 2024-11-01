@php
    use Filament\Support\Enums\Alignment;
@endphp
<x-dynamic-component
    :component="$getFieldWrapperView()"
>
    <div>
        <x-filament-tables::table>
            <x-slot:header>
                <x-filament-tables::row>
                    <x-filament-tables::header-cell>

                    </x-filament-tables::header-cell>
                    @foreach($getRadioOptions() as $options)
                        <x-filament-tables::header-cell :alignment="Alignment::Center" class="!p-2 text-center">
                            {{ $options }}
                        </x-filament-tables::header-cell>
                    @endforeach
                </x-filament-tables::row>
            </x-slot:header>

                @foreach($getChildComponents() as $radio)
                    <x-filament-tables::row>
                        <x-filament-tables::header-cell>
                            {{ $radio }}
                        </x-filament-tables::header-cell>

{{--                        @foreach($radio->getOptions() as $value => $label)--}}
{{--                            <x-filament-tables::cell>--}}
{{--                                {{ dd($radio->getId()) }}--}}
{{--                                <x-filament::input.radio--}}
{{--                                    :valid="! $errors->has($statePath)"--}}
{{--                                    :attributes="--}}
{{--                            \Filament\Support\prepare_inherited_attributes($radio->getExtraInputAttributeBag())--}}
{{--                                ->merge([--}}
{{--                                    'disabled' => $radio->isDisabled() || $radio->isOptionDisabled($value, $label),--}}
{{--                                    'id' => $radio->getId() . '-' . $value,--}}
{{--                                    'name' => $radio->getId(),--}}
{{--                                    'value' => $value,--}}
{{--                                    'wire:loading.attr' => 'disabled',--}}
{{--                                    $applyStateBindingModifiers('wire:model') => $radio->getStatePath(),--}}
{{--                                ], escape: false)--}}
{{--                                ->class(['mt-1'])--}}
{{--                        "--}}
{{--                                />--}}
{{--                            </x-filament-tables::cell>--}}
{{--                        @endforeach--}}

{{--                            {{ $radio->render() }}--}}

                    </x-filament-tables::row>
                @endforeach

        </x-filament-tables::table>
    </div>
{{--    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">--}}
{{--        <!-- Interact with the `state` property in Alpine.js -->--}}
{{--    </div>--}}
</x-dynamic-component>
