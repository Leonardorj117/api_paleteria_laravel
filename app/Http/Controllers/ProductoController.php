<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductoController extends Controller
{
    use AuthorizesRequests;

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
                'categoria' => 'required', // Almacenar la categoría como un arreglo dentro del documento
                'precio' => 'required|numeric|min:0',
                'estado' => 'required|string|in:Activo,Inactivo,Existente,No Existente',
                'existencia' => 'required|integer|min:0',
                'imagen_1' => 'nullable|max:2048',
                'imagen_2' => 'nullable|max:2048',
                'imagen_3' => 'nullable|max:2048',
            ]);

            $producto = new Producto($validated);

            foreach (['imagen_1', 'imagen_2', 'imagen_3'] as $key) {
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
        // $this->authorize('view', $producto);

        return response()->json($producto, 200);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        // $this->authorize('update', $producto);

        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:50',
                'descripcion' => 'required|string|max:255',
                'categoria' => 'required',
                'precio' => 'required|numeric|min:0',
                'estado' => 'required|string|in:Activo,Inactivo,Existente,No Existente',
                'existencia' => 'required|integer|min:0',
                'imagen_1' => 'nullable|max:2048',
                'imagen_2' => 'nullable|max:2048',
                'imagen_3' => 'nullable|max:2048',
            ]);

            $producto->update($validated);

            foreach (['imagen_1', 'imagen_2', 'imagen_3'] as $key) {
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
        $producto = Producto::findOrFail($id);
        // $this->authorize('delete', $producto);
        $producto->delete();

        return response()->json(['message' => 'Producto eliminado con éxito.'], 204);
    }
}
