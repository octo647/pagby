<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'owner_name',
        'email',
        'phone',
        'tipo',
        'salon_name',
        'address',
        'neighborhood',
        'city',
        'state',
    ];
    use HasFactory;
}
