<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $table = 'presences';
    
    protected $fillable = ['user_id', 'user_type', 'date'];

    // Relacionamento polimórfico
    public function user()
    {
        return $this->morphTo();
    }

    protected $casts = [
        'date' => 'datetime',
    ];
}
