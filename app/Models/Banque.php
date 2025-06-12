<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banque extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'code_banque', 'code_guichet', 'numero_compte', 'cle_rib', 'iban', 'code_swift', 'domiciliation', 'telephone'];
}

