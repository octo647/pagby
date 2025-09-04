<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Appointment extends Model
{
    protected $fillable = [
        'employee_id',
        'branch_id',
        'customer_id',
        'services',
        'appointment_date',
        'start_time',
        'end_time',
        'total',
        'notes',
        'cancellation_reason',
        'cancellation_date',
        'cancellation_time',
        'cancellation_by',
        'cancellation_status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'status',
    ];
    protected $casts = [
        'appointment_date' => 'date',
        'services' => 'string',
        
    ];
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function avaliacao()
    {
        return $this->hasOne(\App\Models\Avaliacao::class, 'appointment_id');
    }
    
    protected $table = 'appointments';
 
    use HasFactory;
}
