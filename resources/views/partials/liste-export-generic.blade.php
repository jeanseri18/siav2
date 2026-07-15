<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle ?? 'Liste' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #111; margin: 16px; }
        .header-table { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        .header-table td { vertical-align: top; border: none; padding: 0; }
        .logo { max-width: 90px; max-height: 60px; }
        h1 { font-size: 16px; text-align: center; margin: 0 0 4px; }
        .doc-info { text-align: right; font-size: 9px; line-height: 1.5; }
        table.liste { width: 100%; border-collapse: collapse; page-break-inside: auto; }
        table.liste thead { display: table-header-group; }
        table.liste tr { page-break-inside: avoid; }
        table.liste th, table.liste td { border: 1px solid #333; padding: 4px 3px; text-align: left; vertical-align: top; }
        table.liste th { background: #e8e8e8; font-weight: bold; text-align: center; font-size: 7px; }
        .text-center { text-align: center; }
        .footer-count { margin-top: 10px; font-size: 9px; font-weight: bold; }
    </style>
</head>
<body>
@php
    $pdfBranding = $pdfBranding ?? \App\Support\PdfBranding::forBu(null);
    $logoSrc = $pdfBranding['logo_src'] ?? null;
    $editePar = auth()->user() ? \Illuminate\Support\Str::before(auth()->user()->email, '@') : '—';
    $buNom = $pdfBranding['bu']?->nom ?? '—';
@endphp

<table class="header-table">
    <tr>
        <td style="width: 120px;">
            @if(!empty($logoSrc))
                <img src="{{ $logoSrc }}" alt="Logo" class="logo">
            @elseif(!empty($pdfBranding['logo_absolute_path']))
                <img src="{{ $pdfBranding['logo_absolute_path'] }}" alt="Logo" class="logo">
            @endif
        </td>
        <td><h1>{{ $documentTitle ?? 'Liste' }}</h1></td>
        <td style="width: 180px;">
            <div class="doc-info">
                <div>Date d'édition : {{ now()->format('d/m/Y') }}</div>
                <div>BU : {{ $buNom }}</div>
                <div>Edité par : {{ $editePar }}</div>
            </div>
        </td>
    </tr>
</table>

<table class="liste">
    <thead>
        <tr>
            @foreach($headers as $header)
            <th>{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $row)
        <tr>
            @foreach($row as $cell)
            <td>{{ $cell }}</td>
            @endforeach
        </tr>
        @empty
        <tr>
            <td colspan="{{ count($headers) }}" class="text-center">Aucune donnée</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer-count">Nombre de lignes : {{ str_pad((string) count($rows), 2, '0', STR_PAD_LEFT) }}</div>
</body>
</html>
