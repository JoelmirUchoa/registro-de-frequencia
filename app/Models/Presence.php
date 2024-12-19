<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    //protected $table = 'presences';
    
    protected $fillable = ['user_type', 'user_id', 'created_at', 'updated_at', 'date'];

    // Relacionamento polimórfico
    public function user()
    {
        return $this->morphTo();
    }

    protected $casts = [
        'date' => 'datetime',
    ];
}
