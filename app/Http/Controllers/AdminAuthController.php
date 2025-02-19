<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

use function response;

class AdminAuthController extends Controller
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

            $token = $admin->createToken($request->nombre);

            return response()->json([$admin]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }


    public function login(Request $request)
    {
        dd($request);
        $request->validate([
            'nombre_de_cuenta' => 'required|string',
            'password' => 'required|string',
            'nombre' => 'required|string',
        ]);
        
        // Buscar al admin por nombre de cuenta
        $admin = Admin::where('nombre_de_cuenta', $request->nombre_de_cuenta)->first();

        // Verificar si el admin existe y la contraseña es correcta
        if ($admin && Hash::check($request->password, $admin->password)) {
            // Iniciar sesión manualmente
            Auth::guard('admin')->login($admin);

           
            return response()->json([
                'token' => $admin->createToken(request()->nombre)->plainTextToken
            ]);
            
        }
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }
    public function logout()
    {
        Auth::guard('admin')->logout();
        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }

}
