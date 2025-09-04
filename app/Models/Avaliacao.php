<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;
    protected $table = 'avaliacoes';
    protected $fillable = [
        'user_id',
        'appointment_id',
        'avaliacao',
        'comentario',
        'branch_id',
        'data',
    ];
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function appointment()
    {
        return $this->belongsTo(\App\Models\Appointment::class);
    }
    
}
