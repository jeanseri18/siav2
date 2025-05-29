<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneDemandeCotation extends Model
{
    use HasFactory;

    protected $table = 'lignes_demande_cotation';

    protected $fillable = [
        'demande_cotation_id',
        'article_id',
        'designation',
        'quantite',
        'unite_mesure',
        'specifications'
    ];

    public function demandeCotation()
    {
        return $this->belongsTo(DemandeCotation::class, 'demande_cotation_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}