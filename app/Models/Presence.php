<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    //protected $table = 'presences';
    
    protected $fillable = ['user_type', 'user_id', 'name', 'loja', 'date', 'created_at', 'updated_at'];

    // Relacionamento polimórfico
    public function user()
    {
        return $this->morphTo();
    }

    protected $casts = [
        'date' => 'datetime',
    ];

        // Relacionamento com o modelo Brother
        public function brother()
        {
            return $this->belongsTo(Brother::class, 'user_id', 'id')
                        ->where('user_type', 'brother');
        }
    
        // Relacionamento com o modelo Visitor
        public function visitor()
        {
            return $this->belongsTo(Visitor::class, 'user_id', 'id')
                        ->where('user_type', 'visitor');
        }
}
