@session('error')
    <div class="p-4 border-l-4 border-danger-400 bg-danger-50">
        <div class="flex gap-3">
            <x-heroicon-s-exclamation-triangle class="w-5 h-5 text-danger-400 shrink-0" />

            <p class="text-sm text-danger-700">
                {{ $value }}
            </p>
        </div>
    </div>
@endsession
