<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Cliente extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'clientes';
    protected $fillable = [
        'nombre',
        'apellido_materno',
        'apellido_paterno',
       // 'email',
        'contrasena',
        'estado',
       // 'información',
       // 'dirección',
        'red_social',
        'imagen'
       // 'verificación',
       // 'google_id',
       // 'google_token',
        //'google_refresh_token',
    ];
    use HasFactory;
}
