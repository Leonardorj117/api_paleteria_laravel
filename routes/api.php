<?php

use App\Http\Controllers\ClienteAuthController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;




Route::get('clientes', [ClienteController::class, 'index']);
Route::post('clientes', [ClienteController::class, 'store']);
Route::delete('clientes/{id}', [ClienteController::class, 'destroy']);
Route::post('clientes/{id}', [ClienteController::class, 'update']);


//Amind Login
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');


//Cliente Login
Route::post('/cliente/login', [ClienteAuthController::class, 'login']);
Route::post('/cliente/logout', [ClienteAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('pedidos', PedidoController::class);
Route::get('mis-pedidos', [PedidoController::class, 'misPedidos']);
Route::get('pedidos/{id}/detalle', [PedidoController::class, 'detalle']);

//Administradores routes
Route::prefix('administradores')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::get('/{id}', [AdminController::class, 'show']); // Obtener un administrador por ID
    Route::post('/{id}', [AdminController::class, 'update']); // Actualizar un administrador por ID
    Route::delete('/{id}', [AdminController::class, 'destroy']); // Eliminar un administrador por ID
    Route::post('/', [AdminController::class, 'create']);//Crear un administrador.
});

// Route::middleware('auth:sanctum')->group(function () {
    Route::get('productos', [ProductoController::class, 'index']);
    Route::post('productos', [ProductoController::class, 'store']);
    Route::get('productos/{id}', [ProductoController::class, 'show']);
    Route::post('productos/{id}', [ProductoController::class, 'update']);
    Route::delete('productos/{id}', [ProductoController::class, 'destroy']);
// });