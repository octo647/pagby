<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PagByPayment;

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
        'subscription_plan',
        'contract_accepted_at',
    ];
    use HasFactory;
    public function pagbypayment()
    {
        return $this->hasMany(PagByPayment::class);
    }
    
}
