@extends('layouts.app')

@section('content')
@php
    $isNonConformite = $isNonConformite ?? false;
@endphp
<div class="container-fluid reception-form-page">
    <x-stock-flux-nav module="reception" context="create" />
    <div class="card reception-form-main shadow-sm border-0">
        <div class="reception-form-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h3 class="reception-form-title mb-0 d-flex align-items-center gap-2">
                <i class="fas {{ $isNonConformite ? 'fa-exclamation-triangle' : 'fa-truck-loading' }}"></i>
                @if($isNonConformite)
                    Signaler une non-conformité
                @else
                    Réception des articles
                @endif
            </h3>
            <a href="{{ route('receptions.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>

        <div class="card-body reception-form-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="reception-form-info-card h-100">
                        <div class="reception-form-info-head">
                            <i class="fas fa-file-invoice"></i> Bon de commande
                        </div>
                        <div class="reception-form-info-body">
                            <p class="mb-2"><span class="rf-label">Référence</span><br>
                                <strong class="text-primary">{{ $bonCommande->reference }}</strong></p>
                            <p class="mb-2"><span class="rf-label">Fournisseur</span><br>{{ $bonCommande->fournisseur->nom }}</p>
                            @if($bonCommande->projet)
                                <p class="mb-2"><span class="rf-label">Projet</span><br>
                                    <strong>{{ $bonCommande->projet->nom_projet }}</strong>
                                    @if($bonCommande->projet->ref_projet)
                                        <span class="text-muted small">({{ $bonCommande->projet->ref_projet }})</span>
                                    @endif
                                </p>
                            @endif
                            <p class="mb-0"><span class="rf-label">Date de commande</span><br>
                                {{ \Carbon\Carbon::parse($bonCommande->date_commande)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="reception-form-progress-card h-100">
                        <div class="reception-form-progress-head">
                            <i class="fas fa-chart-line"></i> Progression
                        </div>
                        <div class="reception-form-progress-body">
                            @php
                                $totalQuantite = $bonCommande->lignes->sum('quantite');
                                $totalRecue = $bonCommande->lignes->sum('quantite_recue');
                                $pourcentage = $totalQuantite > 0 ? round(($totalRecue / $totalQuantite) * 100, 1) : 0;
                            @endphp
                            <div class="progress reception-form-progress-track mb-2">
                                <div class="progress-bar reception-form-progress-bar {{ $pourcentage >= 100 ? 'bg-success' : '' }}"
                                     role="progressbar"
                                     style="width: {{ min($pourcentage, 100) }}%"
                                     aria-valuenow="{{ $pourcentage }}"></div>
                            </div>
                            <p class="mb-0 small"><strong>{{ number_format($totalRecue, 0, ',', ' ') }}</strong> / {{ number_format($totalQuantite, 0, ',', ' ') }} unités déjà réceptionnées</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($isNonConformite)
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start gap-2 mb-4">
                    <i class="fas fa-info-circle mt-1"></i>
                    <div>
                        Indiquez la <strong>quantité totale</strong> enregistrée pour chaque ligne concernée, puis la partie <strong>non conforme</strong> (défaut, avarie, écart…). Seule la quantité <strong>conforme</strong> est ajoutée au stock. Une <strong>quantité non conforme &gt; 0</strong> est requise sur au moins une ligne.
                    </div>
                </div>
            @endif

            @if($lignesEnAttente->isEmpty())
                <div class="alert alert-info border-0 shadow-sm">Aucune ligne restant à réceptionner pour ce bon de commande.</div>
            @else
                <form action="{{ $isNonConformite ? route('receptions.non-conformite.store', $bonCommande) : route('receptions.store', ['bonCommande' => $bonCommande]) }}" method="POST" id="receptionForm">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="date_reception" class="form-label fw-semibold">Date de réception <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('date_reception') is-invalid @enderror"
                                   id="date_reception"
                                   name="date_reception"
                                   value="{{ old('date_reception', date('Y-m-d')) }}"
                                   required>
                            @error('date_reception')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="numero_bon_livraison" class="form-label fw-semibold">N° bon de livraison fournisseur</label>
                            <input type="text" class="form-control" id="numero_bon_livraison" name="numero_bon_livraison"
                                   value="{{ old('numero_bon_livraison') }}" placeholder="Optionnel">
                        </div>
                        <div class="col-md-4">
                            <label for="transporteur" class="form-label fw-semibold">Transporteur</label>
                            <input type="text" class="form-control" id="transporteur" name="transporteur"
                                   value="{{ old('transporteur') }}" placeholder="Optionnel">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observations" class="form-label fw-semibold">Observations</label>
                        <textarea class="form-control" id="observations" name="observations" rows="2"
                                  placeholder="{{ $isNonConformite ? 'Détail de la non-conformité (écarts constatés, photos à l\'appui signalées au fournisseur, etc.)' : 'Commentaires sur la livraison, l\'état des colis…' }}">{{ old('observations') }}</textarea>
                    </div>

                    <div class="reception-table-section mb-4">
                        <div class="reception-table-section-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Lignes à réceptionner</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-light" id="btn-tout-cocher">Tout cocher</button>
                                <button type="button" class="btn btn-outline-light" id="btn-tout-decocher">Tout décocher</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table reception-form-table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 42px;" class="text-center">
                                            <input type="checkbox" class="form-check-input" id="check-all-lignes" title="Cocher / décocher tout">
                                        </th>
                                        <th>Article</th>
                                        <th class="text-nowrap">Unité</th>
                                        <th class="text-end">Cmd</th>
                                        <th class="text-end">Déjà reçu</th>
                                        <th class="text-end">Reste</th>
                                        <th style="min-width: 130px;">Qté {{ $isNonConformite ? 'enregistrée' : 'à réceptionner' }}</th>
                                        @if($isNonConformite)
                                            <th style="min-width: 130px;">Dont non conforme</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lignesEnAttente as $ligne)
                                        @php
                                            $quantiteRestante = (float) $ligne->quantite - (float) $ligne->quantite_recue;
                                        @endphp
                                        <tr class="ligne-reception" data-restant="{{ $quantiteRestante }}">
                                            <td class="text-center align-middle">
                                                <input type="checkbox"
                                                       class="form-check-input ligne-inclure"
                                                       data-ligne-id="{{ $ligne->id }}"
                                                       aria-label="Inclure {{ $ligne->article->nom }}">
                                            </td>
                                            <td>
                                                <strong class="reception-article-name">{{ $ligne->article->nom }}</strong>
                                                <br><small class="text-muted">{{ $ligne->article->reference }}</small>
                                            </td>
                                            <td class="align-middle">
                                                @php
                                                    $um = $ligne->article->uniteMesure ?? null;
                                                @endphp
                                                @if($um && ($um->ref || $um->nom))
                                                    <span class="text-nowrap small fw-semibold text-primary">{{ $um->ref ?? $um->nom }}</span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end align-middle reception-form-qty">{{ number_format((float) $ligne->quantite, 2, ',', ' ') }}</td>
                                            <td class="text-end align-middle reception-form-qty">{{ number_format((float) $ligne->quantite_recue, 2, ',', ' ') }}</td>
                                            <td class="text-end align-middle">
                                                <span class="badge bg-warning text-dark">{{ number_format($quantiteRestante, 2, ',', ' ') }}</span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number"
                                                           class="form-control quantite-a-receptionner"
                                                           name="quantites[{{ $ligne->id }}]"
                                                           min="0"
                                                           step="0.01"
                                                           max="{{ $quantiteRestante }}"
                                                           value="0"
                                                           disabled
                                                           data-max="{{ $quantiteRestante }}">
                                                    <button type="button" class="btn btn-outline-secondary btn-max-ligne" disabled title="Mettre le reste à recevoir">Max</button>
                                                </div>
                                            </td>
                                            @if($isNonConformite)
                                                <td>
                                                    <input type="number"
                                                           class="form-control form-control-sm quantite-non-conforme"
                                                           name="non_conformes[{{ $ligne->id }}]"
                                                           min="0"
                                                           step="0.01"
                                                           value="0"
                                                           disabled
                                                           data-nc-max="0">
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p class="text-muted small mb-4">
                        @if($isNonConformite)
                            Les lignes cochées sont enregistrées sur une nouvelle réception. Le stock catalogue et projet est augmenté uniquement des quantités <strong>conformes</strong>.
                        @else
                            Seules les lignes cochées sont prises en compte. Le stock du projet est mis à jour lors de la validation.
                        @endif
                    </p>

                    <div class="d-flex flex-wrap justify-content-between gap-2 mt-4 pt-3 border-top border-light-subtle">
                        <a href="{{ route('receptions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                        <button type="submit" class="btn reception-form-submit text-white">
                            <i class="fas fa-check me-1"></i> {{ $isNonConformite ? 'Enregistrer la non-conformité' : 'Réceptionner' }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@if(!$lignesEnAttente->isEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('receptionForm');
    if (!form) return;

    const isNonConformite = {{ $isNonConformite ? 'true' : 'false' }};
    const checkAll = document.getElementById('check-all-lignes');
    const btnToutCocher = document.getElementById('btn-tout-cocher');
    const btnToutDecocher = document.getElementById('btn-tout-decocher');

    function syncNcMax(row) {
        if (!isNonConformite) return;
        const qty = row.querySelector('.quantite-a-receptionner');
        const nc = row.querySelector('.quantite-non-conforme');
        if (!qty || !nc || nc.disabled) return;
        const q = parseFloat(qty.value) || 0;
        nc.setAttribute('data-nc-max', q);
        let ncv = parseFloat(nc.value) || 0;
        if (ncv > q) {
            nc.value = q;
        }
    }

    function setRowActive(row, actif) {
        const cb = row.querySelector('.ligne-inclure');
        const qty = row.querySelector('.quantite-a-receptionner');
        const btnMax = row.querySelector('.btn-max-ligne');
        const nc = row.querySelector('.quantite-non-conforme');
        const restant = parseFloat(row.getAttribute('data-restant')) || 0;
        if (!qty) return;
        qty.disabled = !actif;
        if (btnMax) btnMax.disabled = !actif;
        if (nc) nc.disabled = !actif;
        if (actif) {
            if (parseFloat(qty.value) <= 0 && restant > 0) {
                qty.value = restant;
            }
            qty.setAttribute('max', restant);
        } else {
            qty.value = 0;
            if (nc) nc.value = 0;
        }
        syncNcMax(row);
    }

    document.querySelectorAll('.ligne-reception').forEach(function(row) {
        const cb = row.querySelector('.ligne-inclure');
        if (!cb) return;
        cb.addEventListener('change', function() {
            setRowActive(row, cb.checked);
        });
        const btnMax = row.querySelector('.btn-max-ligne');
        if (btnMax) {
            btnMax.addEventListener('click', function() {
                const qty = row.querySelector('.quantite-a-receptionner');
                const max = parseFloat(qty.getAttribute('data-max')) || 0;
                if (qty && !qty.disabled) qty.value = max;
                syncNcMax(row);
            });
        }
        const qty = row.querySelector('.quantite-a-receptionner');
        if (qty) {
            qty.addEventListener('input', function() {
                const max = parseFloat(qty.getAttribute('data-max')) || 0;
                let v = parseFloat(qty.value);
                if (isNaN(v) || v < 0) return;
                if (v > max) qty.value = max;
                syncNcMax(row);
            });
        }
        const nc = row.querySelector('.quantite-non-conforme');
        if (nc) {
            nc.addEventListener('input', function() {
                const qtyEl = row.querySelector('.quantite-a-receptionner');
                const q = parseFloat(qtyEl && !qtyEl.disabled ? qtyEl.value : 0) || 0;
                let ncv = parseFloat(nc.value) || 0;
                if (ncv < 0) nc.value = 0;
                if (ncv > q) nc.value = q;
            });
        }
    });

    function setAll(checked) {
        document.querySelectorAll('.ligne-reception').forEach(function(row) {
            const cb = row.querySelector('.ligne-inclure');
            if (cb) {
                cb.checked = checked;
                setRowActive(row, checked);
            }
        });
        if (checkAll) checkAll.checked = checked;
        if (checkAll) checkAll.indeterminate = false;
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            setAll(checkAll.checked);
        });
    }
    if (btnToutCocher) btnToutCocher.addEventListener('click', function() { setAll(true); });
    if (btnToutDecocher) btnToutDecocher.addEventListener('click', function() { setAll(false); });

    form.addEventListener('submit', function(e) {
        let hasQty = false;
        let totalNc = 0;
        document.querySelectorAll('.ligne-reception').forEach(function(row) {
            const cb = row.querySelector('.ligne-inclure');
            const qty = row.querySelector('.quantite-a-receptionner');
            const nc = row.querySelector('.quantite-non-conforme');
            if (cb && cb.checked && qty && !qty.disabled) {
                const v = parseFloat(qty.value);
                if (!isNaN(v) && v > 0) {
                    hasQty = true;
                    if (isNonConformite && nc && !nc.disabled) {
                        totalNc += parseFloat(nc.value) || 0;
                    }
                }
            }
        });
        if (!hasQty) {
            e.preventDefault();
            alert('Cochez au moins une ligne et indiquez une quantité supérieure à zéro.');
            return;
        }
        if (isNonConformite && totalNc <= 0) {
            e.preventDefault();
            alert('Indiquez au moins une quantité non conforme sur une ligne cochée.');
        }
    });
});
</script>
@endif

@push('styles')
<style>
.reception-form-page {
    --rf-primary: #033d71;
    --rf-primary-light: #033d71;
    --rf-primary-dark: #033d71;
}
.reception-form-main {
    border: 1px solid rgba(3, 61, 113, 0.12);
    border-radius: 0.5rem;
    overflow: hidden;
}
.reception-form-header {
    background: linear-gradient(135deg, var(--rf-primary) 0%, var(--rf-primary-dark) 100%);
    color: #fff;
    padding: 1rem 1.25rem;
}
.reception-form-title {
    font-size: 1.15rem;
    font-weight: 700;
}
.reception-form-body {
    background: #f8f9fa;
    padding: 1.25rem 1.25rem 1.5rem;
}
.reception-form-info-card {
    background: #fff;
    border: 1px solid rgba(3, 61, 113, 0.12);
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.06);
}
.reception-form-info-head {
    background: linear-gradient(135deg, var(--rf-primary) 0%, var(--rf-primary-dark) 100%);
    color: #fff;
    padding: 0.65rem 1rem;
    font-weight: 600;
    font-size: 0.9rem;
}
.reception-form-info-body {
    padding: 1rem;
}
.reception-form-progress-card {
    background: #fff;
    border: 1px solid rgba(3, 61, 113, 0.16);
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(3, 61, 113, 0.08);
}
.reception-form-progress-head {
    background: linear-gradient(135deg, var(--rf-primary-light) 0%, var(--rf-primary) 100%);
    color: #fff;
    padding: 0.65rem 1rem;
    font-weight: 600;
    font-size: 0.9rem;
}
.reception-form-progress-body {
    padding: 1rem;
}
.reception-form-progress-track {
    height: 1.35rem;
    background: rgba(3, 61, 113, 0.1);
    border-radius: 50rem;
}
.reception-form-progress-bar {
    background: linear-gradient(90deg, #f39c12 0%, #fd7e14 100%);
}
.rf-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #6c757d;
    font-weight: 600;
}
.reception-table-section {
    background: #fff;
    border: 1px solid rgba(3, 61, 113, 0.12);
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.06);
}
.reception-table-section-header {
    background: linear-gradient(135deg, var(--rf-primary) 0%, var(--rf-primary-dark) 100%);
    color: #fff;
    padding: 0.75rem 1rem;
}
.reception-form-table thead th {
    background: #f1f5f9;
    color: var(--rf-primary-dark);
    font-weight: 600;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    border: none;
    padding: 0.75rem 0.85rem;
}
.reception-form-table tbody td {
    border-color: rgba(3, 61, 113, 0.08);
    vertical-align: middle;
}
.reception-form-table tbody tr:hover {
    background: rgba(10, 140, 255, 0.06);
}
.reception-article-name {
    color: var(--rf-primary-dark);
}
.reception-form-table td.reception-form-qty {
    font-size: 0.875rem;
    font-variant-numeric: tabular-nums;
}
.reception-form-table .form-control:focus {
    border-color: var(--rf-primary-light);
    box-shadow: 0 0 0 0.2rem rgba(10, 140, 255, 0.2);
}
.reception-form-submit {
    background: linear-gradient(135deg, var(--rf-primary-light) 0%, var(--rf-primary-dark) 100%);
    border: none;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
}
.reception-form-submit:hover {
    color: #fff;
    filter: brightness(1.06);
}
</style>
@endpush
@endsection
