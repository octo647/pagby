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
        
    ];
    protected $table = 'appointments';
 
    use HasFactory;
}
