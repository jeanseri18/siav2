<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeTravaux extends Model
{
    use HasFactory;
protected $table = 'type_travaux';
    protected $fillable = ['nom'];
}
