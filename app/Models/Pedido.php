<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Pedido extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'pedidos';

    protected $fillable = [
        'id_cliente', 'total', 'descuento', 'iva', 'metodo_pago', 'id_transaccion', 'estado', 'google_id'
    ];
    
    public function producto_pedidos()
    {
        return $this->hasMany(ProductosPedido::class, 'id_pedido');
    }
}
