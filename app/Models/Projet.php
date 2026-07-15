<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Projet extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref_projet', 'date_creation', 'nom_projet', 'description', 'date_debut', 
        'date_fin', 'client', 'secteur_activite_id', 'conducteur_travaux_id', 
        'chef_projet_id', 'hastva', 'tva_achat', 'montant_global', 'chiffre_affaire_global', 
        'total_depenses', 'statut', 'bu_id', 'pays_id', 'ville_id', 
        'commune_id', 'quartier_id', 'secteur_id', 'created_by', 'updated_by'
    ];

    public function clientFournisseur()
    {
        return $this->belongsTo(ClientFournisseur::class, 'client', 'id');
    }

    public function secteurActivite()
    {
        return $this->belongsTo(SecteurActivite::class);
    }

    public function bu()
    {
        return $this->belongsTo(BU::class);
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'id_projet');
    }

    /**
     * Recalcule les dates du projet à partir de l'enveloppe des contrats (min début, max fin).
     */
    public function syncDatesFromContrats(): void
    {
        if (! $this->exists) {
            return;
        }

        $count = Contrat::where('id_projet', $this->id)->count();
        if ($count === 0) {
            $this->forceFill([
                'date_debut' => null,
                'date_fin' => null,
            ])->save();

            return;
        }

        $minDebut = DB::table('contrats')->where('id_projet', $this->id)->min('date_debut');
        $maxFin = DB::table('contrats')->where('id_projet', $this->id)->max('date_fin');

        $this->forceFill([
            'date_debut' => $minDebut,
            'date_fin' => $maxFin,
        ])->save();
    }

    /**
     * Recalcule montant_global (enveloppe marché), chiffre d'affaires (factures) et dépenses (bons de commande).
     */
    public function syncFinancialAggregates(): void
    {
        if (! $this->exists) {
            return;
        }

        $this->loadMissing(['contrats.dqes']);

        $montantGlobal = (float) $this->contrats->sum(function (Contrat $contrat) {
            $m = (float) ($contrat->montant ?? 0);
            if ($m > 0) {
                return $m;
            }

            $validated = $contrat->dqes
                ->where('statut', 'validé')
                ->sortByDesc(fn ($d) => $d->updated_at?->timestamp ?? $d->created_at?->timestamp ?? 0)
                ->first();

            if ($validated && (float) $validated->montant_total_ttc > 0) {
                return (float) $validated->montant_total_ttc;
            }

            $latest = $contrat->dqes
                ->sortByDesc(fn ($d) => $d->updated_at?->timestamp ?? $d->created_at?->timestamp ?? 0)
                ->first();

            if ($latest && (float) $latest->montant_total_ttc > 0) {
                return (float) $latest->montant_total_ttc;
            }

            return 0.0;
        });

        $contratIds = $this->contrats->pluck('id');
        $chiffreAffaire = $contratIds->isEmpty()
            ? 0.0
            : (float) Facture::whereIn('id_contrat', $contratIds)
                ->where(function ($q) {
                    $q->whereNull('statut')->orWhere('statut', '<>', 'annulée');
                })
                ->sum('montant_total');

        $totalDepenses = (float) BonCommande::where('projet_id', $this->id)
            ->where(function ($q) {
                $q->whereNull('statut')->orWhere('statut', '<>', 'annulée');
            })
            ->sum('montant_total');

        $this->forceFill([
            'montant_global' => $montantGlobal,
            'chiffre_affaire_global' => $chiffreAffaire,
            'total_depenses' => $totalDepenses,
            'updated_by' => auth()->id(),
        ])->save();
    }
    
    public function conducteurTravaux()
    {
        return $this->belongsTo(User::class, 'conducteur_travaux_id');
    }
    
    public function chefProjet()
    {
        return $this->belongsTo(User::class, 'chef_projet_id');
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function quartier()
    {
        return $this->belongsTo(Quartier::class);
    }

    public function secteurLocalisation()
    {
        return $this->belongsTo(Secteur::class, 'secteur_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
