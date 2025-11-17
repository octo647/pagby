<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'owner_name',
        'cpf',
        'email',        
        'phone',
        'notas',
        'tipo',
        'tenant_name',
        'employee_count',
        'address',
        'cep',
        'neighborhood',
        'city',
        'state',
    ];
    use HasFactory;
}
