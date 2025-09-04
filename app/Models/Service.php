<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['created_at','updated_at','photo','salon_id', 'service', 'price', 'time'];
    public function employees(){
        return $this->belongsToMany(User::class, 'service_user', 
        'service_id', 'user_id');
    }
    use HasFactory;
}
