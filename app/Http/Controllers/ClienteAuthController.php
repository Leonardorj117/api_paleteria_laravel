<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ClienteAuthController extends Controller
{
    public function login(Request $request)
    {
        
        try {

            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::guard('cliente')->attempt($request->only('email', 'password'))) {

                $cliente = Auth::guard('cliente')->user();

                $token = $cliente->createToken('ClienteToken')->plainTextToken;

                return response()->json([
                    'user' => $cliente,
                    'token' => $token,
                    'message' => 'Inicio de sesión exitoso',
                ], 200);

            }
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'user' => Auth::user(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        Auth::guard('cliente')->logout();
        return response()->json([
            'message' => 'Session terminada.',
        ]);
    }
}
