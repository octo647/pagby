<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'appointment_id',
        'amount',
        'payment_method',
        'status',
    ];

    // Relacionamento com Appointment
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
