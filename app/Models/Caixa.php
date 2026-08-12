<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    use HasFactory;
    protected $table = 'caixa';
    protected $fillable = [
        'branch_id',
        'data',
        'total_entrada',
        'total_saida',
        'saldo_final',
    ];
    protected $casts = [
        'data' => 'date',
        'total_entrada' => 'decimal:2',
        'total_saida' => 'decimal:2',
        'saldo_final' => 'decimal:2',
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
