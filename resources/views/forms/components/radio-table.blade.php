@php
    use Filament\Support\Enums\Alignment;
@endphp
<x-dynamic-component
    :component="$getFieldWrapperView()"
>
    <div>
        <table class="w-full divide-y divide-gray-950/5 dark:divide-white/20">
            <thead>
                <tr>
                    <th>

                    </th>
                    @foreach($getRadioOptions() as $options)
                        <th class="!p-2 text-center">
                            {{ $options }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($getChildComponents() as $radio)
                    <tr>
                        <th>
                            {{ $radio }}
                        </th>

{{--                        @foreach($radio->getOptions() as $value => $label)--}}
{{--                            <td>--}}
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
{{--                            </td>--}}
{{--                        @endforeach--}}

{{--                            {{ $radio->render() }}--}}

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
{{--    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">--}}
{{--        <!-- Interact with the `state` property in Alpine.js -->--}}
{{--    </div>--}}
</x-dynamic-component>
