<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banque extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'code_banque', 'iban', 'code_swift', 'domiciliation'];
}

