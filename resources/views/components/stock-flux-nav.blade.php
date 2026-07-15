{{--
    Navigation rapide entre les étapes du flux Stock / Achats.
    @param string $module approvisionnement|achat|cotation|bon_commande|reception
    @param string $context list|show|create|edit
    @param string|null $returnUrl Surcharge de l’URL du bouton Retour
--}}
@props([
    'module' => 'approvisionnement',
    'context' => 'list',
    'returnUrl' => null,
])

@php
    $stockDashboard = route('sublayouts_article');
    $listUrls = [
        'approvisionnement' => route('demande-approvisionnements.index'),
        'achat' => route('demande-achats.index'),
        'cotation' => route('demande-cotations.index'),
        'bon_commande' => route('bon-commandes.index'),
        'reception' => route('receptions.index'),
    ];

    if ($module === 'reception') {
        $retourHref = $returnUrl ?? ($context === 'list' ? $stockDashboard : route('receptions.index'));
        $crossLinks = [
            ['url' => $listUrls['bon_commande'], 'label' => 'Bons de commande', 'icon' => 'fa-file-invoice'],
        ];
    } else {
        $retourHref = $returnUrl ?? ($context === 'list' ? $stockDashboard : ($listUrls[$module] ?? $stockDashboard));
        $crossLinks = [];
        foreach ($listUrls as $key => $url) {
            if ($key === $module) {
                continue;
            }
            $crossLinks[] = match ($key) {
                'approvisionnement' => ['url' => $url, 'label' => 'Approvisionnement', 'icon' => 'fa-truck-loading'],
                'achat' => ['url' => $url, 'label' => 'Demandes d\'achat', 'icon' => 'fa-shopping-cart'],
                'cotation' => ['url' => $url, 'label' => 'Cotations', 'icon' => 'fa-calculator'],
                'bon_commande' => ['url' => $url, 'label' => 'Bons de commande', 'icon' => 'fa-file-invoice'],
                'reception' => ['url' => $url, 'label' => 'Réceptions', 'icon' => 'fa-truck'],
            };
        }
    }

    $retourTitle = $context === 'list'
        ? 'Retour au tableau de bord Stock'
        : 'Retour à la liste de ce module';
@endphp

<div class="mb-3 no-print">
    <div class="py-2">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="small text-muted me-1 d-none d-md-inline"><i class="fas fa-route me-1"></i>Flux achat</span>
            <a href="{{ $retourHref }}" class="app-btn app-btn-secondary app-btn-sm" title="{{ $retourTitle }}">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
            @foreach ($crossLinks as $link)
                <a href="{{ $link['url'] }}" class="app-btn app-btn-outline-primary app-btn-sm">
                    <i class="fas {{ $link['icon'] }} me-1"></i> {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>
