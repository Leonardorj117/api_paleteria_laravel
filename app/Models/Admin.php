<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory,HasApiTokens;
    
    protected $connection = 'mongodb';
    protected $collection = 'Admins';

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'nombre_de_cuenta',
        'password',
        'rol',
        'estado',
        'imagen',
    ];

    protected $hidden = [
        'remember_token',
        'password'
    ];
    
   
}
