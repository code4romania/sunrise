@php
    $fields = $getFields();
    $state = $getState();
@endphp

<div class="w-full overflow-x-auto border border-gray-200 rounded-lg dark:border-white/5">
    <table class="w-full divide-y divide-gray-950/5 dark:divide-white/20">
        <thead>
            <tr>
                <th class="!p-2">
                    {{ __('general.field') }}
                </th>

                <th class="!p-2">
                    {{ __('general.old') }}
                </th>

                <th class="!p-2">
                    {{ __('general.new') }}
                </th>
            </tr>
        </thead>
        <tbody>

        @foreach ($fields as $field)
            @php
                $oldValue = data_get($state, "old.{$field}");
                $newValue = data_get($state, "attributes.{$field}");

                if (blank($oldValue) && blank($newValue))
                {
                    continue;
                }

                $excludedFields = [
                    'specialistable_id',
                    'specialistable_type',
                    'addressable_id',
                    'addressable_type',
                    'monitoring_id',
                    'intervention_plan_id',
                    'organization_service_id',
                    'intervention_service_id',
                    'organization_service_intervention_id',
                    'monthly_plan_id',
                    'monthly_plan_service_id',
                    'service_intervention_id'
                ];

                if (in_array($field, $excludedFields))
                {

                    continue;
                }

                $lineData = $getLineData($field, $oldValue, $newValue);

                $oldValue = $lineData->get('old');
                $newValue = $lineData->get('new');

            @endphp

            @if($lineData->get('subFields'))

                @foreach($lineData->get('subFields') as $itemKey)
                    @php
                        $subLineData = $getLineData($itemKey, $oldValue->get($itemKey), $newValue->get($itemKey));
                        $oldSubLineValue = $subLineData->get('old');
                        $newSubLineValue = $subLineData->get('new');

                        if (!$subLineData->get('subfields') &&
                            (
                                (blank($oldSubLineValue) && blank($newSubLineValue)) ||
                                $oldSubLineValue === $newSubLineValue
                            )
                        )
                        {
                            continue;
                        }

                    @endphp

                    @if($subLineData->get('subFields'))
                        @php $headerIsDisplayed = false @endphp
                        @foreach($subLineData->get('subFields') as $subField)
                            @php
                                $subSubLineData = $getLineData($subField, $oldSubLineValue->get($subField), $newSubLineValue->get($subField));
                                if (
                                    (blank($subSubLineData->get('old')) && blank($subSubLineData->get('new'))) ||
                                    $subSubLineData->get('old') === $subSubLineData->get('new')
                                )
                                {
                                    continue;
                                }
                            @endphp

                            @if(!$headerIsDisplayed)
                                <tr>
                                    <th class="p-2">
                                        {{ $lineData->get('label') }}
                                    </th>
                                    <td></td>
                                    <td></td>
                                </tr>

                                @php $headerIsDisplayed = true; @endphp
                            @endif

                            <x-history-line :fieldLabel="$subSubLineData->get('label')"
                                            :oldValue="$subSubLineData->get('old')"
                                            :newValue="$subSubLineData->get('new')">
                            </x-history-line>
                        @endforeach
                    @else

                        <x-history-line :fieldLabel="$subLineData->get('label')"
                                        :oldValue="$oldSubLineValue"
                                        :newValue="$newSubLineValue">
                        </x-history-line>

                    @endif

                @endforeach
            @else
                <x-history-line :fieldLabel="$lineData->get('label')"
                                :oldValue="$oldValue"
                                :newValue="$newValue">
                </x-history-line>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
