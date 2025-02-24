<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    private $db;
    private $collection;

    public function index()
    {
        try {
            $clientes = Cliente::all();
            return response()->json([
                'message' => 'Clientes recuperados con exito.',
                'clientes'=> $clientes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al recuperar clientes',
                'clientes' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validación
            $validated = $request->validate([
                'nombre' => 'required|min:4|max:30',
                'apellido_paterno' => 'required|min:4|max:30',
                'apellido_materno' => 'required|min:4|max:30',
                'contraseña' => 'required|min:6|max:256',
                'estado' => 'required|string',
                'red_social' => 'nullable|max:255',
                'imagen' => 'nullable|max:2048',
            ]);

            // Encriptar contraseña
            $validated['contraseña'] = Hash::make($request->contraseña);
            $validated['imagen'] = 'cliente_default.jpg';

            // Insertar en MongoDB
            $cliente = new Cliente($validated);
            $cliente->save();

            return response()->json([
                'message' => 'Cliente registrado con éxito.',
                'clientes' => $cliente,
            ], 201);


        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error al registrar el cliente',
                'clientes' => $e->getMessage()
            ], 422);
        }
    }

    public function show($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            return response()->json([
                'message' => 'Cliente recuperado con éxito.',
                'clientes' => $cliente
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al recuperar el cliente.',
                'clientes' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $cliente = Cliente::findOrFail($id);

            // Validación
            $validated = $request->validate([
                'nombre' => 'required|min:3|max:50',
                'apellido_materno' => 'required|min:3|max:50',
                'apellido_paterno' => 'required|min:3|max:50',
                'estado' => 'required|string',
                'imagen' => 'nullable|string|max:2048',
            ]);

            // Si se envía una imagen, actualizarla; si no, mantener la actual
            if (!$request->has('imagen')) {
                unset($validated['imagen']);
            }


            // Actualizar cliente
            $cliente->update($validated);

            return response()->json([
                'message' => 'Cliente actualizado con éxito.',
                'clientes' => $cliente,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error al actualizar cliente',
                'clientes' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);

            $cliente->delete();

            return response()->json(
                [
                    'message' => 'cliente eliminado',
                    'clientes' => 'eliminado'
                ],
                200
            );


        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error al eliminar cliente',
                'clientes' => $e->getMessage()
            ], 500);
        }
    }
}
