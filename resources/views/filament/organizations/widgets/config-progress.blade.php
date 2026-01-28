<x-filament-widgets::widget>
    <x-filament::section>
{{--        <div class="space-y-4">--}}
{{--            {{ $this->getWidgetInfolist() }}--}}
{{--            <div class="flex items-center space-x-2 gap-2">--}}
{{--                @php--}}
{{--                    $firstIncompleteItem = false;--}}
{{--                @endphp--}}
{{--                @foreach ($this->getProgressData() as $step)--}}
{{--                    <div class="flex flex-col items-center group w-32 text-center">--}}
{{--                        <a href="{{ $step['link'] }}" class=" items-center space-x-2 group"--}}
{{--                           title="{{ $step['label'] }}">--}}
{{--                            <div class="items-center">--}}
{{--                                @if ($step['completed'])--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-8 text-primary-500" fill="currentColor" viewBox="0 0 20 20">--}}
{{--                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1.707-6.293a1 1 0 011.414 0L14 7.414l-1.293-1.293-3.293 3.293L8 7.414l-1.293 1.293a1 1 0 010 1.414l2.586 2.586z" clip-rule="evenodd" />--}}
{{--                                    </svg>--}}
{{--                                @elseif (!$firstIncompleteItem)--}}
{{--                                    @php--}}
{{--                                        $firstIncompleteItem = true;--}}
{{--                                    @endphp--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-8 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">--}}
{{--                                        <circle cx="12" cy="12" r="10" />--}}
{{--                                    </svg>--}}
{{--                                @else--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">--}}
{{--                                        <circle cx="12" cy="12" r="10" />--}}
{{--                                    </svg>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                            <span class="w-32 text-sm mt-1 text-center break-normal text-balance group-hover:underline">--}}
{{--                                {{ $step['label'] }}--}}
{{--                            </span>--}}
{{--                        </a>--}}
{{--                    </div>--}}

{{--                    @if (!$loop->last)--}}
{{--                        <div class="flex-1 h-1 space-x-2 {{ $step['completed'] ? 'bg-primary-500' : 'bg-gray-400' }}"></div>--}}
{{--                    @endif--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </div>--}}
    </x-filament::section>
</x-filament-widgets::widget>
