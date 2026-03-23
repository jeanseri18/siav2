@extends('layouts.app')

@section('title', 'Budget Prévisionnel - BU')
@section('page-title', 'Budget Prévisionnel - BU ' . $budget->annee)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sublayouts_tresorerie') }}">Trésorerie</a></li>
<li class="breadcrumb-item"><a href="{{ route('bu-budget.index') }}">Budget BU</a></li>
<li class="breadcrumb-item active">{{ $budget->annee }}</li>
@endsection

@section('content')
<div class="app-fade-in">
    @if(session('success'))
    <div class="app-alert app-alert-success">
        <div class="app-alert-icon"><i class="fas fa-check-circle"></i></div>
        <div class="app-alert-content">
            <div class="app-alert-text">{{ session('success') }}</div>
        </div>
        <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="app-alert app-alert-danger">
        <div class="app-alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="app-alert-content">
            <div class="app-alert-text">
                <ul class="m-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <div class="app-card mb-3">
        <div class="app-card-header">
            <h2 class="app-card-title"><i class="fas fa-file-excel me-2"></i>Budget Prévisionnel - Entreprise SIA</h2>
            <div class="app-card-actions">
                <span class="app-badge app-badge-info app-badge-pill">BU: {{ session('selected_bu') }}</span>
                <a href="{{ route('bu-budget.index') }}" class="app-btn app-btn-secondary app-btn-sm">Retour</a>
            </div>
        </div>
        <div class="app-card-body">
            <ul class="nav nav-tabs" role="tablist">
                @php
                    $tabs = [
                        'hypotheses' => 'Hypotheses',
                        'chiffre_affaires' => 'Chiffre_Affaires',
                        'cout_chantiers' => 'Cout_Chantiers',
                        'cout_ventes' => 'Cout_Ventes',
                        'charges_fixes' => 'Charges_Fixes',
                        'investissements_depart' => 'Investissements_Départ',
                        'resultat_previsionnel' => 'Resultat_Prévisionnel',
                        'seuil_rentabilite' => 'Seuil_Rentabilité',
                        'plan_financement_initial' => 'Plan_Financement_Initial',
                    ];
                @endphp
                @foreach($tabs as $key => $label)
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $tab === $key ? 'active' : '' }}" href="{{ route('bu-budget.show', ['budget' => $budget->id, 'tab' => $key]) }}">
                        {{ $label }}
                    </a>
                </li>
                @endforeach
            </ul>

            <div class="pt-3">
                @if($tab === 'hypotheses')
                    <div class="app-card">
                        <div class="app-card-header">
                            <h2 class="app-card-title">Hypotheses</h2>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <form method="POST" action="{{ route('bu-budget.rows.store', $budget) }}" class="d-flex gap-2 align-items-end mb-3">
                                @csrf
                                <input type="hidden" name="tab" value="hypotheses">
                                <input type="hidden" name="sheet" value="hypotheses">
                                <div class="flex-grow-1">
                                    <label class="form-label">Références</label>
                                    <input type="text" name="reference" class="form-control" required>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label">Quantité moyenne</label>
                                    <input type="number" step="1" name="parametre" class="form-control" required>
                                </div>
                                <div style="width: 220px;">
                                    <label class="form-label">Montant moyen</label>
                                    <input type="number" step="0.01" name="amount_decimal" class="form-control" required>
                                </div>
                                <button type="submit" class="app-btn app-btn-primary">Ajouter</button>
                            </form>

                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>Références</th>
                                        <th>Quantité moyenne</th>
                                        <th>Montant moyen</th>
                                        <th style="width: 220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($rows = $rowsBySheet['hypotheses'] ?? [])
                                    @foreach($rows as $row)
                                    <tr>
                                        <td>
                                            <form method="POST" action="{{ route('bu-budget.rows.update', [$budget, $row]) }}" class="d-flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="tab" value="hypotheses">
                                                <input type="text" name="reference" value="{{ $row->reference }}" class="form-control" required>
                                        </td>
                                        <td>
                                                <input type="number" step="1" name="parametre" value="{{ $row->parametre }}" class="form-control" required>
                                        </td>
                                        <td class="d-flex gap-2">
                                                <input type="number" step="0.01" name="amount_decimal" value="{{ $row->amount_decimal }}" class="form-control" required style="max-width: 220px;">
                                                <button type="submit" class="app-btn app-btn-outline-primary app-btn-sm">Enregistrer</button>
                                            </form>
                                            <form method="POST" action="{{ route('bu-budget.rows.delete', [$budget, $row]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tab" value="hypotheses">
                                                <button type="submit" class="app-btn app-btn-outline-danger app-btn-sm">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($tab === 'chiffre_affaires')
                    <div class="app-card">
                        <div class="app-card-header">
                            <h2 class="app-card-title">Chiffre_Affaires</h2>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>N° Ligne</th>
                                        <th>Type de travaux</th>
                                        <th>Nombre</th>
                                        <th>Montant unitaire (FCFA)</th>
                                        <th>Montant annuel (FCFA)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($calc['ca']['rows'] as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $row['type_travaux'] }}</td>
                                        <td>{{ number_format((float) $row['nombre'], 0, ',', ' ') }}</td>
                                        <td>{{ number_format((float) $row['montant_unitaire'], 0, ',', ' ') }}</td>
                                        <td>{{ number_format((float) $row['montant_annuel'], 0, ',', ' ') }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4"><strong>Total CA</strong></td>
                                        <td><strong>{{ number_format((float) $calc['ca']['total'], 0, ',', ' ') }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($tab === 'cout_chantiers')
                    <div class="app-card">
                        <div class="app-card-header">
                            <h2 class="app-card-title">Cout_Chantiers</h2>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <form method="POST" action="{{ route('bu-budget.rows.store', $budget) }}" class="d-flex gap-2 align-items-end mb-3">
                                @csrf
                                <input type="hidden" name="tab" value="cout_chantiers">
                                <input type="hidden" name="sheet" value="cout_chantiers">
                                <div class="flex-grow-1">
                                    <label class="form-label">Poste</label>
                                    <input type="text" name="label" class="form-control" required>
                                </div>
                                <div style="width: 220px;">
                                    <label class="form-label">Montant annuel (FCFA)</label>
                                    <input type="number" step="0.01" name="amount_decimal" class="form-control" required>
                                </div>
                                <button type="submit" class="app-btn app-btn-primary">Ajouter</button>
                            </form>

                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>N° Ligne</th>
                                        <th>Poste</th>
                                        <th>Montant annuel (FCFA)</th>
                                        <th style="width: 220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($rows = $rowsBySheet['cout_chantiers'] ?? [])
                                    @foreach($rows as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('bu-budget.rows.update', [$budget, $row]) }}" class="d-flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="tab" value="cout_chantiers">
                                                <input type="text" name="label" value="{{ $row->label }}" class="form-control" required>
                                        </td>
                                        <td class="d-flex gap-2">
                                                <input type="number" step="0.01" name="amount_decimal" value="{{ $row->amount_decimal }}" class="form-control" required style="max-width: 220px;">
                                                <button type="submit" class="app-btn app-btn-outline-primary app-btn-sm">Enregistrer</button>
                                            </form>
                                            <form method="POST" action="{{ route('bu-budget.rows.delete', [$budget, $row]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tab" value="cout_chantiers">
                                                <button type="submit" class="app-btn app-btn-outline-danger app-btn-sm">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2"><strong>Total coûts travaux</strong></td>
                                        <td colspan="2"><strong>{{ number_format((float) $calc['totaux']['cout_chantiers'], 0, ',', ' ') }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($tab === 'cout_ventes')
                    <div class="app-card">
                        <div class="app-card-header">
                            <h2 class="app-card-title">Cout_Ventes</h2>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <form method="POST" action="{{ route('bu-budget.rows.store', $budget) }}" class="d-flex gap-2 align-items-end mb-3">
                                @csrf
                                <input type="hidden" name="tab" value="cout_ventes">
                                <input type="hidden" name="sheet" value="cout_ventes">
                                <div class="flex-grow-1">
                                    <label class="form-label">Poste</label>
                                    <input type="text" name="label" class="form-control" required>
                                </div>
                                <div style="width: 220px;">
                                    <label class="form-label">Montant annuel (FCFA)</label>
                                    <input type="number" step="0.01" name="amount_decimal" class="form-control" required>
                                </div>
                                <button type="submit" class="app-btn app-btn-primary">Ajouter</button>
                            </form>

                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>N° Ligne</th>
                                        <th>Poste</th>
                                        <th>Montant annuel (FCFA)</th>
                                        <th style="width: 220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($rows = $rowsBySheet['cout_ventes'] ?? [])
                                    @foreach($rows as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('bu-budget.rows.update', [$budget, $row]) }}" class="d-flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="tab" value="cout_ventes">
                                                <input type="text" name="label" value="{{ $row->label }}" class="form-control" required>
                                        </td>
                                        <td class="d-flex gap-2">
                                                <input type="number" step="0.01" name="amount_decimal" value="{{ $row->amount_decimal }}" class="form-control" required style="max-width: 220px;">
                                                <button type="submit" class="app-btn app-btn-outline-primary app-btn-sm">Enregistrer</button>
                                            </form>
                                            <form method="POST" action="{{ route('bu-budget.rows.delete', [$budget, $row]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tab" value="cout_ventes">
                                                <button type="submit" class="app-btn app-btn-outline-danger app-btn-sm">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2"><strong>Total coûts ventes</strong></td>
                                        <td colspan="2"><strong>{{ number_format((float) $calc['totaux']['cout_ventes'], 0, ',', ' ') }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($tab === 'charges_fixes')
                    <div class="app-card">
                        <div class="app-card-header">
                            <h2 class="app-card-title">Charges_Fixes</h2>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <form method="POST" action="{{ route('bu-budget.rows.store', $budget) }}" class="d-flex gap-2 align-items-end mb-3">
                                @csrf
                                <input type="hidden" name="tab" value="charges_fixes">
                                <input type="hidden" name="sheet" value="charges_fixes">
                                <div class="flex-grow-1">
                                    <label class="form-label">Poste</label>
                                    <input type="text" name="label" class="form-control" required>
                                </div>
                                <div style="width: 220px;">
                                    <label class="form-label">Montant annuel (FCFA)</label>
                                    <input type="number" step="0.01" name="amount_decimal" class="form-control" required>
                                </div>
                                <button type="submit" class="app-btn app-btn-primary">Ajouter</button>
                            </form>

                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>N° Ligne</th>
                                        <th>Poste</th>
                                        <th>Montant annuel (FCFA)</th>
                                        <th style="width: 220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($rows = $rowsBySheet['charges_fixes'] ?? [])
                                    @foreach($rows as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('bu-budget.rows.update', [$budget, $row]) }}" class="d-flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="tab" value="charges_fixes">
                                                <input type="text" name="label" value="{{ $row->label }}" class="form-control" required>
                                        </td>
                                        <td class="d-flex gap-2">
                                                <input type="number" step="0.01" name="amount_decimal" value="{{ $row->amount_decimal }}" class="form-control" required style="max-width: 220px;">
                                                <button type="submit" class="app-btn app-btn-outline-primary app-btn-sm">Enregistrer</button>
                                            </form>
                                            <form method="POST" action="{{ route('bu-budget.rows.delete', [$budget, $row]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tab" value="charges_fixes">
                                                <button type="submit" class="app-btn app-btn-outline-danger app-btn-sm">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2"><strong>Total charges fixes</strong></td>
                                        <td colspan="2"><strong>{{ number_format((float) $calc['totaux']['charges_fixes'], 0, ',', ' ') }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($tab === 'investissements_depart')
                    <div class="app-card">
                        <div class="app-card-header">
                            <h2 class="app-card-title">Investissements_Départ</h2>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <form method="POST" action="{{ route('bu-budget.rows.store', $budget) }}" class="d-flex gap-2 align-items-end mb-3">
                                @csrf
                                <input type="hidden" name="tab" value="investissements_depart">
                                <input type="hidden" name="sheet" value="investissements_depart">
                                <div class="flex-grow-1">
                                    <label class="form-label">Poste</label>
                                    <input type="text" name="label" class="form-control" required>
                                </div>
                                <div style="width: 220px;">
                                    <label class="form-label">Montant annuel (FCFA)</label>
                                    <input type="number" step="0.01" name="amount_decimal" class="form-control" required>
                                </div>
                                <button type="submit" class="app-btn app-btn-primary">Ajouter</button>
                            </form>

                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>N° Ligne</th>
                                        <th>Poste</th>
                                        <th>Montant annuel (FCFA)</th>
                                        <th style="width: 220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($rows = $rowsBySheet['investissements_depart'] ?? [])
                                    @foreach($rows as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('bu-budget.rows.update', [$budget, $row]) }}" class="d-flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="tab" value="investissements_depart">
                                                <input type="text" name="label" value="{{ $row->label }}" class="form-control" required>
                                        </td>
                                        <td class="d-flex gap-2">
                                                <input type="number" step="0.01" name="amount_decimal" value="{{ $row->amount_decimal }}" class="form-control" required style="max-width: 220px;">
                                                <button type="submit" class="app-btn app-btn-outline-primary app-btn-sm">Enregistrer</button>
                                            </form>
                                            <form method="POST" action="{{ route('bu-budget.rows.delete', [$budget, $row]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tab" value="investissements_depart">
                                                <button type="submit" class="app-btn app-btn-outline-danger app-btn-sm">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2"><strong>Total Invest. Départ</strong></td>
                                        <td colspan="2"><strong>{{ number_format((float) $calc['totaux']['investissements_depart'], 0, ',', ' ') }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($tab === 'resultat_previsionnel')
                    <div class="app-card">
                        <div class="app-card-header">
                            <h2 class="app-card-title">Resultat_Prévisionnel</h2>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>N° Ligne</th>
                                        <th>Poste</th>
                                        <th>Montant annuel (FCFA)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Chiffre d'affaires total</td>
                                        <td>{{ number_format((float) $calc['resultat_previsionnel']['ca_total'], 0, ',', ' ') }}</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Total coût des chantiers</td>
                                        <td>{{ number_format((float) $calc['resultat_previsionnel']['cout_chantiers'], 0, ',', ' ') }}</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Total charges fixes</td>
                                        <td>{{ number_format((float) $calc['resultat_previsionnel']['charges_fixes'], 0, ',', ' ') }}</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Total charges ventes</td>
                                        <td>{{ number_format((float) $calc['resultat_previsionnel']['cout_ventes'], 0, ',', ' ') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><strong>Résultat net prévisionnel</strong></td>
                                        <td><strong>{{ number_format((float) $calc['resultat_previsionnel']['resultat_net'], 0, ',', ' ') }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="text-muted mt-2">
                                Total charges ventes est affiché mais non soustrait dans le résultat net.
                            </div>
                        </div>
                    </div>
                @elseif($tab === 'seuil_rentabilite')
                    <div class="app-card">
                        <div class="app-card-header">
                            <h2 class="app-card-title">Seuil_Rentabilité</h2>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <form method="POST" action="{{ route('bu-budget.seuil.commentaire', $budget) }}" class="mb-3">
                                @csrf
                                <label for="commentaire" class="form-label">Commentaire</label>
                                <textarea name="commentaire" id="commentaire" rows="3" class="form-control">{{ old('commentaire', $seuilCommentaire) }}</textarea>
                                <div class="mt-2">
                                    <button type="submit" class="app-btn app-btn-primary">Enregistrer</button>
                                </div>
                            </form>

                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>N° Ligne</th>
                                        <th>Poste</th>
                                        <th>Valeur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Charges fixes</td>
                                        <td>{{ number_format((float) $calc['seuil_rentabilite']['charges_fixes'], 0, ',', ' ') }}</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Taux de Marge moyenne chantier</td>
                                        <td>{{ number_format((float) ($calc['seuil_rentabilite']['taux_marge'] * 100), 2, ',', ' ') }}%</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Seuil de rentabilité</td>
                                        <td>{{ number_format((float) $calc['seuil_rentabilite']['seuil'], 0, ',', ' ') }}</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Commentaire</td>
                                        <td>{{ $calc['seuil_rentabilite']['commentaire'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($tab === 'plan_financement_initial')
                    <div class="app-card">
                        <div class="app-card-header">
                            <h2 class="app-card-title">Plan_Financement_Initial</h2>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <form method="POST" action="{{ route('bu-budget.rows.store', $budget) }}" class="d-flex gap-2 align-items-end mb-3">
                                @csrf
                                <input type="hidden" name="tab" value="plan_financement_initial">
                                <input type="hidden" name="sheet" value="plan_financement_initial">
                                <div class="flex-grow-1">
                                    <label class="form-label">Poste</label>
                                    <input type="text" name="label" class="form-control" required>
                                </div>
                                <div style="width: 220px;">
                                    <label class="form-label">Valeur</label>
                                    <input type="number" step="0.01" name="amount_decimal" class="form-control" required>
                                </div>
                                <button type="submit" class="app-btn app-btn-primary">Ajouter</button>
                            </form>

                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>N° Ligne</th>
                                        <th>Poste</th>
                                        <th>Montant annuel (FCFA)</th>
                                        <th style="width: 220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($rows = $rowsBySheet['plan_financement_initial'] ?? [])
                                    @foreach($rows as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('bu-budget.rows.update', [$budget, $row]) }}" class="d-flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="tab" value="plan_financement_initial">
                                                <input type="text" name="label" value="{{ $row->label }}" class="form-control" required>
                                        </td>
                                        <td class="d-flex gap-2">
                                                <input type="number" step="0.01" name="amount_decimal" value="{{ $row->amount_decimal }}" class="form-control" required style="max-width: 220px;">
                                                <button type="submit" class="app-btn app-btn-outline-primary app-btn-sm">Enregistrer</button>
                                            </form>
                                            <form method="POST" action="{{ route('bu-budget.rows.delete', [$budget, $row]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tab" value="plan_financement_initial">
                                                <button type="submit" class="app-btn app-btn-outline-danger app-btn-sm">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2"><strong>Total Financement Initial</strong></td>
                                        <td colspan="2"><strong>{{ number_format((float) $calc['totaux']['financement_initial'], 0, ',', ' ') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Écart vs Total Invest. Départ</td>
                                        <td colspan="2">{{ number_format((float) $calc['plan_financement_initial']['ecart_vs_invest'], 0, ',', ' ') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
