<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model as Authenticatable ;

class Administrador extends Authenticatable
{
    protected $connection = 'mongodb';
    protected $collection = 'clientes';

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'nombre_de_cuenta',
        'contrasena',
        'rol',
        'estado',
        'imagen',
    ];

    protected $hidden = [
        'contrasena',
        'rol',
    ];
    
    use HasFactory;
}
