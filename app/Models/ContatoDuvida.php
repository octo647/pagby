<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContatoDuvida extends Model
{
    use HasFactory;

    protected $table = 'contato_duvidas';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'mensagem', // pode ser null
    ];
}