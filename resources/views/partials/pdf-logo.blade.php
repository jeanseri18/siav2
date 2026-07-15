{{-- Logo BU pour PDF DomPDF : dimensions calculées (ratio conservé) --}}
@php
    $pdfBranding = $pdfBranding ?? [];
    $logoSrc = $pdfBranding['logo_src'] ?? null;
    $logoPath = $pdfBranding['logo_absolute_path'] ?? null;
    $dims = \App\Support\PdfBranding::logoDisplaySize(
        $logoPath,
        (int) ($maxWidth ?? 170),
        (int) ($maxHeight ?? 90)
    );
    $logoW = $dims['width'];
    $logoH = $dims['height'];
    $logoClass = $logoClass ?? 'logo';
    $logoAlt = $logoAlt ?? 'Logo';
@endphp
@if(!empty($logoSrc))
    <img
        src="{{ $logoSrc }}"
        alt="{{ $logoAlt }}"
        class="{{ $logoClass }}"
        width="{{ $logoW }}"
        height="{{ $logoH }}"
        style="width: {{ $logoW }}px; height: {{ $logoH }}px;"
    >
@elseif(!empty($logoPath))
    <img
        src="{{ $logoPath }}"
        alt="{{ $logoAlt }}"
        class="{{ $logoClass }}"
        width="{{ $logoW }}"
        height="{{ $logoH }}"
        style="width: {{ $logoW }}px; height: {{ $logoH }}px;"
    >
@endif
