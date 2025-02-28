<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use MongoDB\BSON\ObjectId;

class AdminController extends Controller
{
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
                'imagen1' => 'nullable|image|max:2048',
            ]);

            $validated['password'] = Hash::make($request->password);
            $validated['imagen1'] = 'admin_default.jpg'; // Imagen por defecto

            $admin = Admin::create($validated);

            if ($request->hasFile('imagen1')) {
                $img = $request->file('imagen1');
                $filename = "admin_{$admin->_id}.{$img->extension()}"; // Mongo usa `_id`, no `id`
                $path = $img->storeAs('imagenes/admins', $filename, 'public');

                $admin->imagen1 = asset("storage/$path");
                $admin->save();
            }

            return response()->json([
                'message' => 'Admin registrado con éxito.',
                'admins' => $admin,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error al registrar el admin',
                'admins' => $e->getMessage()
            ], 422);
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

    public function update(Request $request, $id)
    {
        try {
            $admin = Admin::findOrFail($id);

            $validated = $request->validate([
                'nombre' => 'required|min:3|max:50',
                'apellido_materno' => 'required|min:3|max:50',
                'apellido_paterno' => 'required|min:3|max:50',
                'nombre_de_cuenta' => 'required|min:3|max:50',
                'email' => 'required|email|max:50',
                'rol' => 'required|string',
                'estado' => 'required|string',
                'imagen1' => 'nullable|image|max:2048',
            ]);

            $admin->update($validated);

            if ($request->hasFile('imagen1')) {
                $img = $request->file('imagen1');
                $filename = "admin_{$admin->_id}.{$img->extension()}";
                $path = $img->storeAs('imagenes/admins', $filename, 'public');

                $admin->imagen1 = asset("storage/$path");
                $admin->save();
            }

            return response()->json([
                'message' => 'Admin actualizado con éxito.',
                'Administrador' => $admin,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el Admin.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $objectId = new ObjectId($id);

            $admin = Admin::where('_id', $objectId)->first();

            if (!$admin) {
                return response()->json([
                    'message' => 'Admin no encontrado',
                    'admins' => null
                ], 404);
            }
            $admin->delete();

            return response()->json(
                [
                    'message' => 'admin eliminado',
                    'admins' => 'eliminado'
                ],
                200
            );


        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error al eliminar admin',
                'admins' => $e->getMessage()
            ], 500);
        }
    }
}
