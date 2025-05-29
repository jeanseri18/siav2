<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BUAssociat extends Model
{
    use HasFactory;
    protected $table = 'bu_associats';

    protected $fillable = ['bu_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bu()
    {
        return $this->belongsTo(BU::class);
    }
}
