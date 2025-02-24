<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{

    public function create(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'nombre' => 'required|min:3|max:50',
                    'apellido_materno' => 'required|min:3|max:50',
                    'apellido_paterno' => 'required|min:3|max:50',
                    'nombre_de_cuenta' => 'required|min:3|max:50',
                    'password' => 'required|min:6|max:256',
                    'rol' => 'required|string',
                    'estado' => 'required|string',
                    'imagen' => 'nullable|max:2048',
                ]
            );
            // Encriptar password
            $validated['password'] = Hash::make($request->password);
            $validated['imagen'] = 'cliente_default.jpg';

            // Insertar en MongoDB
            $admin = Admin::create($validated);

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
                    'rol' => 'required|string',
                    'estado' => 'required|string',
                    'imagen' => 'nullable|max:2048',
                ]
            );

            // Si se envía una imagen, actualizarla; si no, mantener la actual
            if (!$request->has('imagen')) {
                unset($validated['imagen']);
            }



            // Actualizar cliente
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
            $admin = Admin::findOrFail($id);

            if (!$admin) {
                return response()->json([
                    'message' => 'Error al encontrar el Administrator',
                    'Administrador' => 'Admin no encontrado'
                ], 404);
            }
            $admin->delete();

            return response()->json([
                'message' => 'Administrador eliminado con exito.',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al elimnar el Administrator',
                'Administrador' => $e->getMessage()
            ], 500);
        }


    }
}
