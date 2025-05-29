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
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
