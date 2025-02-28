<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Eloquent\SoftDeletes;


class Cliente extends Authenticatable
{

    use HasFactory,HasApiTokens, SoftDeletes,Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'clientes';
    protected $primaryKey = '_id';
    protected $fillable = [
        'id',
        'nombre',
        'apellido_materno',
        'apellido_paterno',
        'email',
        'password',
        'estado',
        'red_social',
        'imagen1',
    ];

    protected $casts = [
        '_id' => 'string',
    ];
}
