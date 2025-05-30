<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'duration', // Duração em dias
        'features', // JSON ou texto com as características do plano
    ];
    protected $casts = [
        'features' => 'array', // Converte o campo features para um array
        'services' => 'array', // Converte o campo services para um array
        'additional_services' => 'array', // Converte o campo additional_services para um array
    ];
    public function salon()
    {
        return $this->belongsTo(Branch::class);
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    
}
