<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    // Definindo explicitamente o nome da tabela (caso seja diferente de 'presences')
    protected $table = 'presences'; // Pode ser alterado se o nome da tabela for diferente

    // Definindo quais campos podem ser preenchidos (evitar Mass Assignment)
    protected $fillable = ['user_type', 
    'user_id', // Pode ser 'brother' ou 'visitor'
    'date'];   // ID do irmão ou visitante

    // Definindo os tipos de dados (caso seja necessário)
    protected $casts = [
        'date' => 'datetime', // Garantir que o campo 'date' seja tratado como uma data
    ];
}
