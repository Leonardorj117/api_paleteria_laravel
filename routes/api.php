<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;





Route::get('/ping', function (Request  $request) {    
    $connection = DB::connection('mongodb');
    $msg = 'MongoDB is accessible!';
    try {  
        $connection->command(['ping' => 1]);  
    } catch (\Exception  $e) {  
        $msg = 'MongoDB is not accessible. Error: ' . $e->getMessage();
    }
    return ['msg' => $msg];
});

Route::get('/productos_buscar', [ProductoController::class, 'show']);

Route::resource('productos', ProductoController::class)->only([
    'store'
]);

Route::get('clientes',[ClienteController::class,'index']);
Route::post('clientes',[ClienteController::class,'store']);
Route::delete('clientes/{id}',[ClienteController::class,'destroy']);
Route::put('clientes/{id}',[ClienteController::class,'update']);


//Amind Login
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('pedidos', PedidoController::class);
Route::get('mis-pedidos', [PedidoController::class, 'misPedidos']);
Route::get('pedidos/{id}/detalle', [PedidoController::class, 'detalle']);

Route::get('productos', [ProductoController::class, 'index']);
Route::post('productos', [ProductoController::class, 'store']);
Route::get('productos/{id}', [ProductoController::class, 'show']);
Route::put('productos/{id}', [ProductoController::class, 'update']);
Route::delete('productos/{id}', [ProductoController::class, 'destroy']);


//Administradores routes
Route::prefix('administradores')->group(function () {
    Route::get('/', [AdminController::class, 'index']); 
    Route::get('/{id}', [AdminController::class, 'show']); // Obtener un administrador por ID
    Route::put('/{id}', [AdminController::class, 'update']); // Actualizar un administrador por ID
    Route::delete('/{id}', [AdminController::class, 'destroy']); // Eliminar un administrador por ID
    Route::post('/', [AdminController::class, 'create']);
});