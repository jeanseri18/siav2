<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegimeImposition extends Model
{
    use HasFactory;

    protected $fillable = ['nom','ref','tva'];
}
