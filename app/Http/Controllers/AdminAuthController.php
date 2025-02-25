<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
class AdminAuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {

                $admin = Auth::guard('admin')->user();

                $token = $admin->createToken('AdminToken')->plainTextToken;

                return response()->json([
                    'user' => $admin,
                    'token' => $token,
                    'message' => 'Inicio de sesión exitoso',
                ], 200);
                
            }
        } catch (\Exception $e) {
            return response()->json([
                'user' => Auth::user(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return response()->json([
            'message' =>'Session terminada.',
        ]);
    }

}
