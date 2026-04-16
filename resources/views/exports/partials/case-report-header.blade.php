<header>
    <div class="header-strip">
        @if(! empty($branding['header_url']))
            <img src="{{ $branding['header_url'] }}" alt="header">
        @endif
    </div>
    <div class="header-content">
        <h1 class="title">{{ $reportTitle }}</h1>
        <p class="meta">Număr caz {{ $caseId }}</p>
    </div>
</header>
