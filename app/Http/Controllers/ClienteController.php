<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    private $db;
    private $collection;

    public function __construct()
    {
        $client = new Client(env('MONGO_DSN', 'mongodb://localhost:27017'));
        $this->db = $client->selectDatabase(env('MONGO_DATABASE', 'mi_base'));
        $this->collection = $this->db->selectCollection(env('MONGO_COLLECTION', 'clientes'));
    }

    public function index()
    {
        $clientes = Cliente::all(); // En lugar de $this->collection->find()
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
            $insertResult = $this->collection->insertOne($validated);
            $cliente = $this->collection->findOne(['_id' => $insertResult->getInsertedId()]);

            // Subir imagen si existe
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->storeAs(
                    'imagenes/clientes',
                    "cliente_{$cliente['_id']}." . $request->file('imagen')->extension(),
                    'public'
                );
                $this->collection->updateOne(
                    ['_id' => $cliente['_id']],
                    ['$set' => ['imagen' => asset("storage/$path")]]
                );
            }

            return response()->json($cliente, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $cliente = $this->collection->findOne(['_id' => $id]);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        try {
            $cliente = $this->collection->findOne(['_id' => $id]);

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

            // Encriptar contraseña si se proporciona
            if ($request->filled('contraseña')) {
                $validated['contraseña'] = Hash::make($request->contraseña);
            }

            // Manejo de imagen
            if ($request->hasFile('imagen')) {
                if (isset($cliente['imagen']) && $cliente['imagen'] !== 'cliente_default.jpg') {
                    $oldPath = str_replace('storage/', '', $cliente['imagen']);
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('imagen')->storeAs(
                    'imagenes/clientes',
                    "cliente_{$id}." . $request->file('imagen')->extension(),
                    'public'
                );
                $validated['imagen'] = asset("storage/$path");
            }

            // Actualizar cliente
            $this->collection->updateOne(['_id' => $id], ['$set' => $validated]);

            return response()->json([
                'message' => 'Cliente actualizado con éxito.',
                'cliente' => $this->collection->findOne(['_id' => $id]),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $cliente = $this->collection->findOne(['_id' => $id]);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        // Eliminar imagen si no es la predeterminada
        if (isset($cliente['imagen']) && $cliente['imagen'] !== 'cliente_default.jpg') {
            $oldPath = str_replace('storage/', '', $cliente['imagen']);
            Storage::disk('public')->delete($oldPath);
        }

        // Eliminar cliente
        $this->collection->deleteOne(['_id' => $id]);

        return response()->json(null, 204);
    }
}
