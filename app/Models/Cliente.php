<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Cliente extends Model
{

    protected $connection = 'mongodb';
    protected $fillable = [
        'nombre',
        'apellido_materno',
        'apellido_paterno',
        'imagen',
        'email',
        'contraseña',
        'estado',
        'información',
        'dirección',
        'red_social',
        'verificación',
        'google_id',
        'google_token',
        'google_refresh_token',
    ];
    use HasFactory;
}
