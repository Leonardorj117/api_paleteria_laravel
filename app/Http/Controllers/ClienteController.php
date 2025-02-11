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
        // Obtener todos los clientes
        $clientes = Cliente::all();

        return response()->json($clientes);
    }

    public function store(Request $request)
    {
        try {
            // Validación
            $validated = $request->validate([
                'nombre' => 'required|min:3|max:50',
                'apellido_materno' => 'required|min:3|max:50',
                'apellido_paterno' => 'required|min:3|max:50',
                'correo' => 'required|email|max:100',
                'contraseña' => 'required|min:6|max:256',
                'estado' => 'required|string',
                'informacion' => 'nullable|max:255',
                'direccion' => 'nullable|max:255',
                'red_social' => 'nullable|max:255',
                'verificacion' => 'nullable|boolean',
                'imagen' => 'nullable|max:2048',
            ]);

            // Encriptar contraseña
            $validated['contraseña'] = Hash::make($request->contraseña);
            $validated['imagen'] = 'cliente_default.jpg';

            // Insertar en MongoDB
            $cliente = new Cliente($validated);
            $cliente->save();

            return response()->json($cliente, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);

        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);

            if (!$cliente) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }

            // Validación
            $validated = $request->validate([
                'nombre' => 'required|min:3|max:50',
                'apellido_materno' => 'required|min:3|max:50',
                'apellido_paterno' => 'required|min:3|max:50',
                'correo' => "required|email|max:100",
                'contraseña' => 'nullable|min:6|max:256',
                'estado' => 'required|string',
                'informacion' => 'nullable|max:255',
                'direccion' => 'nullable|max:255',
                'red_social' => 'nullable|max:255',
                'verificacion' => 'nullable|boolean',
                'imagen' => 'nullable|image|max:2048',
            ]);
            

            // Actualizar cliente
            $cliente->update($validated);

            return response()->json([
                'message' => 'Cliente actualizado con éxito.',
                'cliente' => $cliente,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        // Eliminar cliente
        $cliente->delete();

        return response()->json(null, 204);
    }
}
