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
        'start_date',
        'end_date',
        'branch_id',
        'created_by',
        'updated_by',
        'status',
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
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
    
    public function isActive()
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
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
