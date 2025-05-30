<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['created_at','updated_at','salon_id', 'service', 'price', 'time'];
    public function employee(){
        return $this->belongsToMany(User::class, 'service_user', 
        'user_id', 'service_id');
    }
    use HasFactory;
}
