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
    public function login(Request $request)
    {
        dd($request);
        $request->validate([
            'nombre_de_cuenta' => 'required|string',
            'password' => 'required|string',
            'nombre' => 'required|string',
        ]);
        
       
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
