<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;

class ProductoController extends Controller
{
    // use AuthorizesRequests;

    public function index()
    {
        try {
            $productos = Producto::all();
            return response()->json([
            'message' => 'Productos recuperados con exito.',
            'productos'=> $productos
        ], 200);
        } catch (\Exception $e){
        return response()->json([
            'message' => 'Error al recuperar los productos.',
            'productos'=> $e->getMessage()
        ], 200);
        }
    }

    public function store(Request $request)
    {

        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:50',
                'descripcion' => 'required|string|max:255',
                'categoria' => 'required',
                'precio' => 'required|numeric|min:0',
                'estado' => 'required|string|in:Activo,Inactivo,Existente,No Existente',
                'existencia' => 'required|integer|min:0',
                'imagen1' => 'nullable|image|max:2048',
                'imagen2' => 'nullable|image|max:2048',
                'imagen3' => 'nullable|image|max:2048',
            ]);

            $producto = new Producto($validated);

            foreach (['imagen1', 'imagen2', 'imagen3'] as $key) {
                if ($request->hasFile($key)) {
                    $img = $request->file($key);
                    $path = $img->storeAs('imagenes/productos', "{$key}_" . time() . ".{$img->extension()}", 'public');
                    $producto->$key = asset("storage/$path");
                }
            }

            $producto->save();

            return response()->json([
                'message' => 'Producto creado con éxito.', 
                'producto' => $producto], 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function show($id)
    {
        $producto = Producto::findOrFail($id);

        return response()->json($producto, 200);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:50',
                'descripcion' => 'required|string|max:255',
                'categoria' => 'required',
                'precio' => 'required|numeric|min:0',
                'estado' => 'required|string|in:Activo,Inactivo,Existente,No Existente',
                'existencia' => 'required|integer|min:0',
                'imagen1' => 'nullable|max:2048',
                'imagen2' => 'nullable|max:2048',
                'imagen3' => 'nullable|max:2048',
            ]);

            $producto->update($validated);

            foreach (['imagen1', 'imagen2', 'imagen3'] as $key) {
                if ($request->hasFile($key)) {
                    $img = $request->file($key);
                    $path = $img->storeAs('imagenes/productos', "{$key}_" . time() . ".{$img->extension()}", 'public');
                    $producto->$key = asset("storage/$path");
                }
            }

            $producto->save();

            return response()->json(['message' => 'Producto actualizado con éxito.', 'producto' => $producto], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $objectId = new ObjectId($id);

            $producto = Producto::where('_id', $objectId)->first();

            if (!$producto) {
                return response()->json([
                    'message' => 'Producto no encontrado',
                    'productos' => null
                ], 404);
            }
            $producto->delete();

            return response()->json(
                [
                    'message' => 'producto eliminado',
                    'productos' => 'eliminado'
                ],
                200
            );


        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error al eliminar producto',
                'productos' => $e->getMessage()
            ], 500);
        }
    }
}
