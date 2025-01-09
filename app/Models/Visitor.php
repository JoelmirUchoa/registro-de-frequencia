<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
    'sim',
    'name',
    'position',
    'loja',
    'numero_da_loja',
    ];
}
