<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuBudgetExcel extends Model
{
    use HasFactory;

    protected $table = 'bu_budget_excels';

    protected $fillable = [
        'bu_id',
        'annee',
    ];

    protected $casts = [
        'annee' => 'integer',
    ];

    public function bu()
    {
        return $this->belongsTo(BU::class, 'bu_id');
    }

    public function values()
    {
        return $this->hasMany(BuBudgetExcelValue::class, 'bu_budget_excel_id');
    }
}
