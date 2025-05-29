<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransfertStock extends Model
{
    use HasFactory;

    protected $table = 'transfert_stock';

    protected $fillable = [
        'id_projet_source',
        'id_projet_destination',
        'article_id',
        'quantite',
        'date_transfert',
    ];

    public function projetSource()
    {
        return $this->belongsTo(Projet::class, 'id_projet_source');
    }

    public function projetDestination()
    {
        return $this->belongsTo(Projet::class, 'id_projet_destination');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
