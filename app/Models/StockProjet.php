<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockProjet extends Model
{
    
    use HasFactory;
    protected $table = 'stock_projet';

    protected $fillable = [
        'id_projet',
        'article_id',
        'quantite',
        'unite_mesure_id',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function uniteMesure()
    {
        return $this->belongsTo(UniteMesure::class, 'unite_mesure_id');
    }
}
