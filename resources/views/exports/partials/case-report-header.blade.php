<header>
    <div class="header-strip">
        @php($headerSrc = $branding['header_src'] ?? $branding['header_url'] ?? null)
        @if(! empty($headerSrc))
            <img src="{{ $headerSrc }}" alt="header">
        @endif
    </div>
    <div class="header-content">
        <h1 class="title">{{ $reportTitle }}</h1>
        <p class="meta">Număr caz {{ $caseNumber ?? $caseId }}</p>
    </div>
</header>
