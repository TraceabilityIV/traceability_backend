<?php

use App\Models\MensajesChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PqrsController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\CultivosController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\PaisesController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ComisionesController;
use App\Http\Controllers\AgrupadoresController;
use App\Http\Controllers\Api\BarriosController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\EstadosController;
use App\Http\Controllers\Api\PedidosController;
use App\Http\Controllers\Api\PreciosController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\CiudadesController;
use App\Http\Controllers\Api\PermisosController;
use App\Http\Controllers\MensajesChatController;
use App\Http\Controllers\Api\EvidenciasController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\SubagrupadoresController;
use App\Http\Controllers\Api\DireccionesController;
use App\Http\Controllers\Api\CostosEnviosController;
use App\Http\Controllers\GaleriasCultivosController;
use App\Http\Controllers\Api\DepartamentosController;
use App\Http\Controllers\TrazabilidadCultivosController;
use App\Http\Controllers\Api\CalificacionPedidosController;
use App\Http\Controllers\Api\TrazabilidadTransportesController;
use App\Http\Controllers\CultivosPredefinidosController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeepsekController;
use App\Http\Controllers\FactoresController;
use App\Http\Controllers\ForgotPasswordController;

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
Route::post('deepseek/search', [DeepsekController::class, 'search']);

//?rutas finales
Route::post('/usuario/token', [UsuarioController::class, 'token']);
Route::post('/usuario/resgistrar', [UsuarioController::class, 'resgistrar']);
Route::post('/usuario/google', [UsuarioController::class, 'google']);
Route::post('/usuario/documentacion_valida', [UsuarioController::class, 'documentacionValida']);

//ciudades
Route::resource("/ciudades", CiudadesController::class)->only('index');
//barrios
Route::post("/barrios/directo", [BarriosController::class, 'directo']);
Route::post("/ciudades/directo", [CiudadesController::class, 'directo']);
//categorias landing
Route::post("/categorias/principales", [CategoriasController::class, 'index']);
Route::post("/productos", [CultivosController::class, 'productos']);
Route::post('/productos/mapa', [CultivosController::class, 'productos_mapa']);
Route::post("/productos/detalle/{id}", [CultivosController::class, 'detalle']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('api.reset-password');

//!Rutas con autenticación
Route::middleware('auth:sanctum')->group(function () {

    Route::resource("/permisos", PermisosController::class);
    Route::resource("/menu", MenuController::class);

    //roles
    Route::get("/roles/all", [RolesController::class, 'all']);
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
    Route::post('/usuario/validar', [UsuarioController::class, 'validation']);
    Route::post('/usuario/usuario_actual', [UsuarioController::class, 'usuarioActual']);

    //perfil
    Route::resource("/perfil", PerfilController::class);

    //paises
    Route::resource("/paises", PaisesController::class);

    //departamentos
    Route::resource("/departamentos", DepartamentosController::class);

    //ciudades
    Route::resource("/ciudades", CiudadesController::class)->except('index');

    //barrios
    Route::resource("/barrios", BarriosController::class);

    //direcciones
    Route::resource("/direcciones", DireccionesController::class);

    //pqrs
    Route::resource("/pqrs", PqrsController::class)->only(['index', 'store', 'destroy, show']);

    //agrupadores
    Route::resource("/agrupadores", AgrupadoresController::class);

    //subagrupadores
    Route::resource("/subagrupadores", SubagrupadoresController::class);

    //categorias
    Route::get("/categorias/mas_vendidas", [CategoriasController::class, 'masVendidas']);
    Route::resource("/categorias", CategoriasController::class);

    //costos envios
    Route::get("/costos_envio/tipos_costos", [CostosEnviosController::class, 'tipos_costos']);
    Route::get("/costos_envio/categorias", [CostosEnviosController::class, 'categorias']);
    Route::get("/costos_envio/{id}/categorias", [CostosEnviosController::class, 'costo_categorias']);
    Route::resource("/costos_envio", CostosEnviosController::class);

    //comisiones
    Route::get("/comisiones/tipos_precios", [ComisionesController::class, 'tipos_precios']);
    Route::resource("/comisiones", ComisionesController::class);


    //cultivos
    Route::get("/cultivos/distribucion_precios", [CultivosController::class, 'distribucionPrecios']);
    Route::get("/cultivos/categorias", [CultivosController::class, 'categorias']);
    Route::get("/cultivos/usuarios", [CultivosController::class, 'usuarios']);
    Route::get("/cultivos/favoritos", [CultivosController::class, 'favoritos']);
    Route::resource("/cultivos", CultivosController::class);
    Route::post("/cultivos/favorito/{id}", [CultivosController::class, 'favorito']);
    Route::delete("/cultivos/favorito/{id}", [CultivosController::class, 'destroyFavorito']);
	//cultivos_predefinidos
	Route::get("/cultivos_predefinidos/externo", [CultivosPredefinidosController::class, 'externo']);
	Route::resource("/cultivos_predefinidos", CultivosPredefinidosController::class);

	Route::post("/cultivos/ia", [CultivosController::class, 'ia']);
	Route::post("/cultivos/recomendaciones", [CultivosController::class, 'recomendaciones']);

	//factores
	Route::resource("/factores", FactoresController::class);

    //precios
    Route::resource("/precios/cultivos", PreciosController::class);

    //galerias de cultivos
    Route::resource("/galerias", GaleriasCultivosController::class);

    //trazabilidad de cultivos
    Route::prefix('trazabilidad/')->name('trazabilidad.')->group(function () {
        Route::resource('/cultivos', TrazabilidadCultivosController::class);
		Route::post("/cultivos/resumen", [TrazabilidadCultivosController::class, 'resumen']);
        Route::resource('/evidencias', EvidenciasController::class);
    });

    //estados
    Route::resource('/estados', EstadosController::class);

    //pedidos
    Route::post('/pedidos/avanzar/{id}', [PedidosController::class, 'avanzar']);
    Route::resource('/pedidos/trazabilidad', TrazabilidadTransportesController::class);
    Route::resource('/pedidos/calificacion', CalificacionPedidosController::class);
    Route::resource('/pedidos', PedidosController::class);


    //chatbot
    Route::resource('/chatbot', ChatbotController::class);

    //mensaje chat
    Route::resource('/mensajes_chat', MensajesChatController::class);

    //notificaciones
    Route::resource('/notificaciones', NotificacionesController::class);

    //Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/cultivos-caros', [DashboardController::class, 'cultivosCaros']);
        Route::get('/tendencia-pedidos', [DashboardController::class, 'tendenciaPedidos']);
        Route::get('/estadisticas', [DashboardController::class, 'estadisticas']);
    });

    //Api deepSek

});
