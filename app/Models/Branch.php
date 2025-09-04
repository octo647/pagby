<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_name',
        'cnpj',
        'require_advance_payment',
        'address',
        'complement',
        'phone',
        'whatsapp',
        'email',
        'city',
        'state',
        'logo',
    ];
    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
