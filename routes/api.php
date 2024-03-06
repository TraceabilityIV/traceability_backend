<?php

use App\Http\Controllers\Api\PermisosController;
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

Route::middleware('auth:sanctum')->get('/obtenerUsuarios', function (Request $request) {
    return response()->json([
        'all' => 'SI'
    ]);
});

//?rutas finales
Route::post('/usuario/token', [UsuarioController::class, 'token']);
Route::post('/usuario/resgistrar', [UsuarioController::class, 'resgistrar']);

//!Rutas con autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::resource("/permisos", PermisosController::class);
});
