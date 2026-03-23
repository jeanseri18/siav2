<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuBudgetExcelRow extends Model
{
    use HasFactory;

    protected $table = 'bu_budget_excel_rows';

    protected $fillable = [
        'bu_budget_excel_id',
        'sheet',
        'reference',
        'parametre',
        'label',
        'amount_decimal',
        'sort_order',
    ];

    protected $casts = [
        'amount_decimal' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function budget()
    {
        return $this->belongsTo(BuBudgetExcel::class, 'bu_budget_excel_id');
    }
}
