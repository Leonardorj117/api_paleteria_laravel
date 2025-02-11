<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdministradorController extends Controller
{
   
    public function index()
    {
     $admin = Administrador::all();
     return response()->json($admin);
    }

    
    public function store(Request $request)
    {
        try {
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

            // Insertar en MongoDB
            $admin = new Administrador($validated);
            $admin->save();
            return response()->json($admin);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

  
    public function show( $id)
    {
        $admin = Administrador::findOrFail($id);

        return response()->json($admin);
    }

    public function update(Request $request, $id)
    {
     try {
        $admin = Administrador::findOrFail($id);

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
        $admin = Administrador::findOrFail($id);

        if (!$admin) {
            return response()->json(['error' => 'Admin no encontrado'], 404);
        }

        
        $admin->delete();

        return response()->json(null, 204);
    }
}
