<footer>
    <div class="footer-strip">
        <span class="footer-cell footer-left">
                @if(! empty($branding['logo_url']))
                <img src="{{ $branding['logo_url'] }}" alt="logo" class="footer-logo">
                @endif
        </span>
        <span class="footer-cell footer-center">Printat la {{ $branding['printed_at'] }}</span>
        <span class="footer-cell footer-right"></span>
    </div>
</footer>
