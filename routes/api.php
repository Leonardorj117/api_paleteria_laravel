<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\AdministradorController;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


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

Route::apiResource('pedidos', PedidoController::class);
Route::get('mis-pedidos', [PedidoController::class, 'misPedidos']);
Route::get('pedidos/{id}/detalle', [PedidoController::class, 'detalle']);

Route::get('productos', [ProductoController::class, 'index']);
Route::post('productos', [ProductoController::class, 'store']);
Route::get('productos/{id}', [ProductoController::class, 'show']);
Route::put('productos/{id}', [ProductoController::class, 'update']);
Route::delete('productos/{id}', [ProductoController::class, 'destroy']);

Route::prefix('administradores')->group(function () {
    Route::get('/', [AdministradorController::class, 'index']); // Obtener todos los administradores
    Route::post('/', [AdministradorController::class, 'store']); // Crear un nuevo administrador
    Route::get('/{id}', [AdministradorController::class, 'show']); // Obtener un administrador por ID
    Route::put('/{id}', [AdministradorController::class, 'update']); // Actualizar un administrador por ID
    Route::delete('/{id}', [AdministradorController::class, 'destroy']); // Eliminar un administrador por ID
});