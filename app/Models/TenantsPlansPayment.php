<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantsPlansPayment extends Model
{
     protected $table = 'tenants_plans_payments';

    protected $fillable = [
        'external_id',
        'mp_payment_id',
        'tenant_id',
        'plan',
        'amount',
        'status',
        'payment_method',
        'payment_type',
        'payer_data',
        'mercadopago_data',
        'approved_at',
        'expires_at',
    ];
}
