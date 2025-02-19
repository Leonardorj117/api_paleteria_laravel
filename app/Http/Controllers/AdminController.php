<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
   
    public function index()
    {
        try {
            $admin = Admin::all();
            return response()->json($admin);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

  
    public function show( $id)
    {
        $admin = Admin::findOrFail($id);

        return response()->json($admin);
    }

    public function update(Request $request, $id)
    {
     try {
        $admin = Admin::findOrFail($id);

        if (!$admin) {
            return response()->json(['error' => 'admin no encontrado'], 404);
        }

        $validated = $request->validate(
            [
                'nombre' => 'required|min:3|max:50',
                'apellido_materno' => 'required|min:3|max:50',
                'apellido_paterno' => 'required|min:3|max:50',
                'nombre_de_cuenta' => 'required|min:3|max:50',
                'contraseña' => 'required|min:6|max:256',
                'rol' => 'required|string',
                'estado' => 'required|string',
                'imagen' => 'nullable|max:2048',
            ]);
            // Encriptar contraseña
        $validated['contraseña'] = Hash::make($request->contraseña);
        $validated['imagen'] = 'cliente_default.jpg';
   


        // Actualizar cliente
        $admin->update($validated);

        return response()->json([
            'message' => 'Admin actualizado con éxito.',
            'cliente' => $admin,
        ], 200);


     } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
     }
    }
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);

        if (!$admin) {
            return response()->json(['error' => 'Admin no encontrado'], 404);
        }

        
        $admin->delete();

        return response()->json("eliminado", 204);
    }
}
