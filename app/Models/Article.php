<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'reference_fournisseur',
        'nom',
        'type',
        'quantite_stock',
        'prix_unitaire',
        'unite_mesure',
        'cout_moyen_pondere',
        'categorie_id',
        'sous_categorie_id',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function sousCategorie()
    {
        return $this->belongsTo(SousCategorie::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(ClientFournisseur::class, 'reference_fournisseur');
    }

    public function uniteMesure()
    {
        return $this->belongsTo(UniteMesure::class, 'unite_mesure');
    }
    
    // Relation pour les lignes de demande de ravitaillement
    public function lignesDemandeRavitaillement()
    {
        return $this->hasMany(LigneDemandeRavitaillement::class);
    }

    // Relation pour les lignes de réception
    public function lignesReception()
    {
        return $this->hasMany(LigneReception::class);
    }

    // Récupère la date du dernier approvisionnement
    public function getDateDernierApprovisionnementAttribute()
    {
        return $this->lignesReception()
            ->join('receptions', 'ligne_receptions.reception_id', '=', 'receptions.id')
            ->whereIn('receptions.statut', ['en_cours', 'complete', 'partielle'])
            ->where('ligne_receptions.quantite_recue', '>', 0)
            ->orderBy('receptions.date_reception', 'desc')
            ->value('receptions.date_reception');
    }

    // Récupère le prix du dernier achat
    public function getPrixDernierAchatAttribute()
    {
        // Si le prix unitaire est déjà maintenu (mise à jour au moment des réceptions),
        // on le renvoie en priorité.
        if ($this->prix_unitaire !== null && (float) $this->prix_unitaire > 0) {
            return (float) $this->prix_unitaire;
        }

        // Sinon, on calcule à partir de la dernière ligne de réception (même si la réception est partielle).
        return $this->lignesReception()
            ->join('receptions', 'ligne_receptions.reception_id', '=', 'receptions.id')
            ->whereIn('receptions.statut', ['en_cours', 'complete', 'partielle'])
            ->where('ligne_receptions.quantite_recue', '>', 0)
            ->orderBy('receptions.date_reception', 'desc')
            ->orderBy('ligne_receptions.created_at', 'desc')
            ->value('ligne_receptions.prix_unitaire_recu');
    }

    /**
     * Recalcule et persiste le dernier prix d'achat et le coût moyen pondéré à partir des lignes de réception.
     *
     * - Dernier achat : prix de la ligne de réception la plus récente (quantité > 0, prix > 0).
     * - CMP : Σ (prix_unitaire_recu × quantite_recue) / Σ (quantite_recue) sur toutes les lignes éligibles.
     * Les réceptions encore « en_cours » sont incluses : sans cela le CMP restait à 0 tant que le statut
     * n’était pas repassé en complete/partielle, ou si seules des réceptions partielles existaient.
     */
    public function recalculerPrixAchatDepuisReceptions(int $max = 5): void
    {
        $statutsEligibles = ['en_cours', 'complete', 'partielle'];

        $cmpPondere = (float) (DB::table('ligne_receptions as lr')
            ->join('receptions as r', 'lr.reception_id', '=', 'r.id')
            ->where('lr.article_id', $this->id)
            ->whereNull('lr.deleted_at')
            ->whereNull('r.deleted_at')
            ->whereIn('r.statut', $statutsEligibles)
            ->where('lr.quantite_recue', '>', 0)
            ->whereNotNull('lr.prix_unitaire_recu')
            ->where('lr.prix_unitaire_recu', '>', 0)
            ->selectRaw('COALESCE(SUM(lr.prix_unitaire_recu * lr.quantite_recue) / NULLIF(SUM(lr.quantite_recue), 0), 0) as cmp')
            ->value('cmp') ?? 0.0);

        $dernierPrix = DB::table('ligne_receptions as lr')
            ->join('receptions as r', 'lr.reception_id', '=', 'r.id')
            ->where('lr.article_id', $this->id)
            ->whereNull('lr.deleted_at')
            ->whereNull('r.deleted_at')
            ->whereIn('r.statut', $statutsEligibles)
            ->where('lr.quantite_recue', '>', 0)
            ->whereNotNull('lr.prix_unitaire_recu')
            ->where('lr.prix_unitaire_recu', '>', 0)
            ->orderByDesc('r.date_reception')
            ->orderByDesc('lr.id')
            ->value('lr.prix_unitaire_recu');

        if ($cmpPondere <= 0 && $dernierPrix === null) {
            return;
        }

        $this->forceFill([
            'prix_unitaire' => $dernierPrix !== null ? (float) $dernierPrix : $this->prix_unitaire,
            'cout_moyen_pondere' => $cmpPondere > 0 ? $cmpPondere : (float) ($dernierPrix ?? $this->cout_moyen_pondere ?? 0),
        ])->save();
    }
}