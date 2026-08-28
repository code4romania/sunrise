<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
        $pdf->page_text(500, 818, 'Pagina {PAGE_NUM}/{PAGE_COUNT}', $font, 9, [75 / 255, 85 / 255, 99 / 255]);
    }
</script>
