<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'plan_id',
        'mp_payment_id',
        'start_date',
        'end_date',
        'branch_id',
        'created_by',
        'updated_by',
        'status',   
        'mp_payment_id',      // ADICIONAR se não existir
        'payment_method',     // ADICIONAR se não existir  
        'payment_status',     // ADICIONAR se não existir
        'mp_data'            // ADICIONAR se não existir
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'mp_data' => 'array' 
    ];
    


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class)
            ->with('additionalServices'); // Inclui os serviços adicionais do plano
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->ends_at && 
               $this->ends_at->isFuture();
    }
       public function hasApprovedPayment(): bool
    {
        return $this->payment_status === 'approved';
    }
    
    public function isExpired()
    {
        return $this->end_date < now();
    }
    public function isUpcoming()
    {
        return $this->start_date > now();
    }
    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }






}
