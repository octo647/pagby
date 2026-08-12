<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanMonthlyRevenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'branch_id',
        'month',
        'revenue',
    ];

    protected $dates = ['month'];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
