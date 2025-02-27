<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use HasFactory,HasApiTokens, SoftDeletes;
    
    protected $connection = 'mongodb';
    protected $collection = 'admins';
    protected $primaryKey = '_id';

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'nombre_de_cuenta',
        'email',
        'password',
        'rol',
        'estado',
        'imagen',
    ];

    
    protected $hidden = [
        'remember_token',
        'password'
    ];
    
    protected $casts = [
        '_id' => 'string', // Conviértelo a string para que se muestre en JSON
    ];
   
}
