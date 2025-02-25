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
        $pedidos = Pedido::with('cliente')->get();
        return response()->json($pedidos);
    }

    public function show($id)
    {
        $pedido = Pedido::with('cliente')->find($id);
        return response()->json($pedido);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $productos = collect($request->productos)->map(function ($producto) {
                return [
                    'nombre' => $producto['nombre'],
                    'producto_id' => $producto['producto_id'],
                    'cantidad' => $producto['cantidad'],
                    'costo_unitario' => $producto['costo_unitario'],
                    'descuento' => $producto['descuento'],
                    'costo_total' => $producto['costo_unitario'] * $producto['cantidad'] - $producto['descuento'],
                ];
            });

            $pedido = Pedido::create([
                'id_cliente' => $request->id_cliente,
                'total' => $request->total,
                'descuento' => $request->descuento,
                'iva' => $request->iva,
                'metodo_pago' => $request->metodo_pago,
                'id_transaccion' => $request->id_transaccion,
                'estado' => $request->estado,
                'google_id' => $request->google_id,
                'productos' => $productos,
            ]);

            DB::commit();
            return response()->json(['message' => 'Pedido creado correctamente', 'pedido' => $pedido], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $pedido = Pedido::find($id);
        if ($pedido) {
            $pedido->delete();
            return response()->json(['message' => 'Pedido eliminado correctamente.']);
        }
        return response()->json(['error' => 'Pedido no encontrado'], 404);
    }

    public function misPedidos()
    {
        $pedidos = Pedido::where('id_cliente', Auth::id())->get();
        return response()->json($pedidos);
    }

    public function detalle($id)
    {
        $pedido = Pedido::findOrFail($id);
        return response()->json($pedido);
    }
}
