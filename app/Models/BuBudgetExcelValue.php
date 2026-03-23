<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuBudgetExcelValue extends Model
{
    use HasFactory;

    protected $table = 'bu_budget_excel_values';

    protected $fillable = [
        'bu_budget_excel_id',
        'sheet',
        'key',
        'value_decimal',
        'value_text',
    ];

    protected $casts = [
        'value_decimal' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(BuBudgetExcel::class, 'bu_budget_excel_id');
    }
}
