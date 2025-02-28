<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use MongoDB\BSON\ObjectId;

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
            $validated = $request->validate([
                'nombre' => 'required|min:4|max:30',
                'apellido_paterno' => 'required|min:4|max:30',
                'apellido_materno' => 'required|min:4|max:30',
                'email' => 'required|email|max:50',
                'password' => 'required|min:6|max:256',
                'estado' => 'required|string',
                'red_social' => 'nullable|max:255',
                'imagen1' => 'nullable|image|max:2048',
            ]);

            $validated['password'] = Hash::make($request->password);
            $validated['imagen1'] = 'cliente_default.jpg';

            $cliente = Cliente::create($validated);

            if ($request->hasFile('imagen1')) {
                $img = $request->file('imagen1');
                $filename = "cliente_{$cliente->_id}.{$img->extension()}";
                $path = $img->storeAs('imagenes/clientes', $filename, 'public');

                $cliente->imagen1 = asset("storage/$path");
                $cliente->save();
            }

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

            $validated = $request->validate([
                'nombre' => 'required|min:3|max:50',
                'apellido_materno' => 'required|min:3|max:50',
                'apellido_paterno' => 'required|min:3|max:50',
                'estado' => 'required|string',
                'imagen1' => 'nullable|image|max:2048',
            ]);

            $cliente->update($validated);

            if ($request->hasFile('imagen1')) {
                $img = $request->file('imagen1');
                $filename = "cliente_{$cliente->_id}.{$img->extension()}";
                $path = $img->storeAs('imagenes/clientes', $filename, 'public');

                $cliente->imagen1 = asset("storage/$path");
                $cliente->save();
            }

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
            $objectId = new ObjectId($id);

            $cliente = Cliente::where('_id', $objectId)->first();

            if (!$cliente) {
                return response()->json([
                    'message' => 'Cliente no encontrado',
                    'clientes' => null
                ], 404);
            }
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
