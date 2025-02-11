<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Producto extends Model
{   
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'productos'; // Especificar la colección en MongoDB si es necesario

    protected $fillable = [
        'nombre', 
        'descripcion', 
        'categoria', 
        'precio', 
        'estado', 
        'existencia', 
        'imagen_1', 
        'imagen_2', 
        'imagen_3'
    ];
}