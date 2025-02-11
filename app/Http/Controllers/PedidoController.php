<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['cliente', 'producto_pedidos.producto'])->get();
        return response()->json($pedidos, 200);
    }

    public function show($id)
    {
        $pedido = Pedido::with(['cliente', 'producto_pedidos.producto'])->findOrFail($id);
        return response()->json($pedido, 200);
    }
    
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido = Pedido::create($request->only([
                'id_cliente', 'total', 'descuento', 'iva', 'metodo_pago', 'id_transaccion', 'estado', 'google_id'
            ]));

            foreach ($request->productos as $producto) {
                ProductosPedido::create([
                    'id_pedido' => $pedido->_id, 
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'descuento' => $producto['descuento'],
                ]);
            }
            
            DB::commit();
            return response()->json(['message' => 'Pedido creado con éxito.', 'pedido' => $pedido], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->producto_pedidos()->delete();
        $pedido->delete();

        return response()->json(['message' => 'Pedido eliminado correctamente.'], 204);
    }

    public function misPedidos()
    {
        $pedidos = Pedido::where('id_cliente', Auth::id())->get();
        return response()->json($pedidos, 200);
    }

    public function detalle($id)
    {
        $pedido = Pedido::with('productos')->findOrFail($id);
        return response()->json($pedido, 200);
    }
}