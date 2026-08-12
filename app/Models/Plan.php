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
        'allowed_days', // JSON ou texto com os dias permitidos (array/json)
        'duration_days', // Duração em dias
        'features', // JSON ou texto com as características do plano
        'created_by', // ID do usuário que criou o plano
    ];
    protected $casts = [
        'features' => 'array', // Converte o campo features para um array
        'allowed_days' => 'array', // Converte o campo allowed_days para um array
        //'services' => 'array', // Converte o campo services para um array
        'additional_services' => 'array', // Converte o campo additional_services para um array
        
    ];
    
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)
            ->where('status', 'active'); // Apenas assinaturas ativas
    }
    public function services()
    {
        return $this->belongsToMany(Service::class, 'plan_service', 'plan_id', 'service_id');
    }

    public function additionalServices()
    {
        return $this->belongsToMany(Service::class, 'plan_additional_service')
            ->withPivot('discount');  
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
    
}
