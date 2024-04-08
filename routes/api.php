<?php

use App\Http\Controllers\Api\BarriosController;
use App\Http\Controllers\Api\CiudadesController;
use App\Http\Controllers\Api\DepartamentosController;
use App\Http\Controllers\Api\DireccionesController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\PaisesController;
use App\Http\Controllers\Api\PermisosController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\PqrsController;
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

    //roles
    Route::resource("/roles", RolesController::class);
    Route::get('/roles/{id}/permisos', [RolesController::class, 'permisos']);
    Route::post('/role/{rol}/permiso/{permiso}', [RolesController::class, 'permisoRol']);
    Route::post('/roles/{id}/sincronizarPermisos', [RolesController::class, 'sincronizarPermisos']);
    Route::post('/roles/{id}/agregarPermisos', [RolesController::class, 'agregarPermisos']);

    //usuarios
    Route::resource("/usuarios", UsuarioController::class);
    Route::get('/usuario/roles', [UsuarioController::class, 'roles']);
    Route::post('/usuario/logout', [UsuarioController::class, 'logout']);
    Route::post('/usuario/asignarRol', [UsuarioController::class, 'asignarRol']);
    Route::post('/usuario/validar', [UsuarioController::class, 'validar']);

    //paises
    Route::resource("/paises", PaisesController::class);

    //departamentos
    Route::resource("/departamentos", DepartamentosController::class);

    //ciudades
    Route::resource("/ciudades", CiudadesController::class);

    //barrios
    Route::resource("/barrios", BarriosController::class);

    //direcciones
    Route::resource("/direcciones", DireccionesController::class);

    //pqrs
    Route::resource("/pqrs", PqrsController::class)->only(['index', 'store', 'destroy']);

});
