<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brother extends Model
{
    use HasFactory;

    // Definindo explicitamente o nome da tabela (caso seja diferente de 'brothers')
    protected $table = 'brothers';

    // Definindo quais campos podem ser preenchidos (evitar Mass Assignment)
    protected $fillable = [
        'sim', 
        'name', 
        'position',
        'loja',
        'numero_da_loja',
    ];
}
