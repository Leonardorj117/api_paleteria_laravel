<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\ObjectId;

class ClienteController extends Controller
{
    private $db;
    private $collection;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']); // Requiere token excepto en el registro
    }

    public function index()
    {
        try {
            $clientes = Cliente::all();
            return response()->json([
                'message' => 'Clientes recuperados con exito.',
                'clientes' => $clientes
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
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|min:4|max:30',
            'apellido_paterno' => 'required|min:4|max:30',
            'apellido_materno' => 'required|min:4|max:30',
            'email' => 'required|email|max:50|unique:clientes',
            'password' => 'required|min:6|max:256',
            'estado' => 'required|string',
            'red_social' => 'nullable|max:255',
            'imagen' => 'nullable|string|max:2048',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'clientes' => $validator->errors(),
            ], 422);
        }

        try {

            $data = $request->all();
            $data['password'] = Hash::make($request->password);

            if ($request->hasFile('imagen')) {
                $data['imagen'] = $request->file('imagen')->store('clientes', 'public');
            } else {
                $data['imagen'] = 'cliente_default.jpg';
            }

            $cliente = Cliente::create($data);

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
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json([
                'message' => 'cliente no encontrado',
                'clientes' => '',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|min:4|max:30',
            'apellido_paterno' => 'sometimes|required|min:4|max:30',
            'apellido_materno' => 'sometimes|required|min:4|max:30',
            'email' => 'sometimes|required|email|max:50|unique:clientes,email,' . $cliente->id,
            'password' => 'sometimes|required|min:6|max:256',
            'estado' => 'sometimes|required|string',
            'red_social' => 'nullable|max:255',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'clientes' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $request->all();

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('imagen')) {
                $data['imagen'] = $request->file('imagen')->store('clientes', 'public');
            }

            $cliente->update($data);

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
