<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
class AdminAuthController extends Controller
{

    public function login(Request $request)
    {
      try{
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            return response()->json([
                'user' => Auth::user(),
                'message' => 'Incio de sesion correcto.',
            ],200);
        }
      }catch(\Exception $e){
        return response()->json([
            'user'=> Auth::user(),
            'message'=> $e->getMessage(),
        ],500);
      }
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::guard('admin')->attempt($credentials)) {
    //         return redirect()->intended('/admin/dashboard');
    //     }

    //     return back()->withErrors(['email' => 'Credenciales incorrectas']);
    // }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }

}
