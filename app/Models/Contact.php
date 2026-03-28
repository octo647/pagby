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
        'tenant_id',
        'employee_count',
        'address',
        'number',
        'complement',
        'cep',
        'neighborhood',
        'city',
        'state',
        'subscription_plan',
        'contract_accepted_at',
        'asaas_customer_id',
    ];
    use HasFactory;
    public function pagbypayment()
    {
        return $this->hasMany(PagByPayment::class);
    }
    
}
