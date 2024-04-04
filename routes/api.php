<?php

use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\PermisosController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//?rutas finales
Route::post('/usuario/token', [UsuarioController::class, 'token']);
Route::post('/usuario/resgistrar', [UsuarioController::class, 'resgistrar']);
Route::post('/usuario/google', [UsuarioController::class, 'google']);

//!Rutas con autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::resource("/permisos", PermisosController::class);
    Route::resource("/menu", MenuController::class);
    Route::resource("/roles", RolesController::class);

    Route::resource("/usuarios", UsuarioController::class);
    Route::get('/usuario/roles', [UsuarioController::class, 'roles']);
    Route::post('/usuario/logout', [UsuarioController::class, 'logout']);
    Route::post('/usuario/validar', [UsuarioController::class, 'validar']);
});
