<div class="flex items-center space-x-2">
    <h2 class="text-xl font-bold">{{ $title }}</h2>
    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-sm font-medium bg-{{ $enum->getColor()[100] }}-100 text-{{ $enum->getColor()[800] }}-800">
        {{ $enum->getLabel() }}
    </span>
</div>

<div class="flex space-x-2">
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
