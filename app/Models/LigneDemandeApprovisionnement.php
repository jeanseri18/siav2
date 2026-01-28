<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneDemandeApprovisionnement extends Model
{
    use HasFactory;

    protected $table = 'lignes_demande_approvisionnement';

    protected $fillable = [
        'demande_approvisionnement_id',
        'article_id',
        'quantite_demandee',
        'quantite_approuvee',
        'commentaire'
    ];

    protected $casts = [
        'quantite_demandee' => 'integer',
        'quantite_approuvee' => 'integer',
    ];

    public function demandeApprovisionnement()
    {
        return $this->belongsTo(DemandeApprovisionnement::class, 'demande_approvisionnement_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}