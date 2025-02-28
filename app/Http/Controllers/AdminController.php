<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use MongoDB\BSON\ObjectId;

class AdminController extends Controller
{

    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|min:3|max:50',
                'apellido_materno' => 'required|min:3|max:50',
                'apellido_paterno' => 'required|min:3|max:50',
                'nombre_de_cuenta' => 'required|min:3|max:50',
                'email' => 'required|email|max:50',
                'password' => 'required|min:6|max:256',
                'rol' => 'required|string',
                'estado' => 'required|string',
                'imagen1' => 'nullable|string|max:2048',
            ]);

            // Encriptar contraseña
            $validated['password'] = Hash::make($request->password);
            $validated['imagen1'] = 'cliente_default.jpg'; // Imagen por defecto

            // Crear el usuario en MongoDB primero
            $admin = Admin::create($validated);

            // Verificar si hay una imagen subida
            if ($request->hasFile('imagen1')) {
                $img = $request->file('imagen1');
                $filename = "admin_{$admin->_id}.{$img->extension()}"; // Mongo usa `_id`, no `id`
                $path = $img->storeAs('imagenes/admins', $filename, 'public');

                // Actualizar la URL de la imagen
                $admin->imagen1 = asset("storage/$path");
                $admin->save();
            }

            return response()->json([$admin]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function index()
    {
        try {
            $admin = Admin::all();
            return response()->json([
                'message' => 'Administradores recuperados con exito.',
                'Administradores' => $admin
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al recuperar los administradores.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $admin = Admin::findOrFail($id);

            return response()->json([
                'message' => 'Administrador obtenido con exito.',
                'Administrador' => $admin
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el Administrador.',
                'Administrador' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request,$id)
    {
        try {
            $admin = Admin::findOrFail($id);

            $validated = $request->validate(
                [
                    'nombre' => 'required|min:3|max:50',
                    'apellido_materno' => 'required|min:3|max:50',
                    'apellido_paterno' => 'required|min:3|max:50',
                    'nombre_de_cuenta' => 'required|min:3|max:50',
                    'email' => 'required|email|max:50',
                    'rol' => 'required|string',
                    'estado' => 'required|string',
                    'imagen' => 'nullable|max:2048',
                ]
            );

            if (!$request->has('imagen')) {
                unset($validated['imagen']);
            }

            $admin->update($validated);

            return response()->json([
                'message' => 'Admin actualizado con éxito.',
                'Administrador' => $admin,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el Admin.',
                'Administrador' => $e->getMessage()
            ], 500);
        }

    }

    public function destroy($id)
    {
        try {
            // Convertir el ID a ObjectId correctamente
            $mongoId = new ObjectId($id);
    
            // Buscar el administrador
            $admin = Admin::where('_id', $mongoId)->first();
    
            if (!$admin) {
                return response()->json(['message' => 'Admin no encontrado'], 404);
            }
    
            // Eliminar el administrador
            $admin->delete();
    
            return response()->json(['message' => 'Administrador eliminado con éxito'], 200);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar', 'error' => $e->getMessage()], 500);
        }
    }
}
