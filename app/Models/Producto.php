<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Producto extends Model
{   
    protected $connection = 'mongodb';
    protected $fillable = ['nombre', 'descripcion', 'imagen1', 'existencia', 'precio'];
    use HasFactory;
}
