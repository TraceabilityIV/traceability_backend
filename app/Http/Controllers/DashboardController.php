<?php

namespace App\Http\Controllers;

use App\Models\Cultivos;
use App\Models\Pedido;
use App\Models\HistorialEstadosPedido;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Obtiene los cultivos con precios más altos
     */
    public function cultivosCaros(Request $request)
    {
        try {
            $limit = $request->limit ?? 5;
            
            $cultivos = Cultivos::whereNotNull('precio_venta')
                ->orderByDesc('precio_venta')
                ->with('cultivo_predefinido')
                ->limit($limit)
                ->get()
                ->map(function ($cultivo) {
                    return [
                        'id' => $cultivo->id,
                        'nombre' => $cultivo->cultivo_predefinido->nombre ?? 'Sin nombre',
                        'nombre_corto' => $cultivo->nombre_corto ?? $cultivo->cultivo_predefinido->nombre_corto ?? 'Sin nombre',
                        'precio_venta' => $cultivo->precio_venta,
                    ];
                });
            
            return response()->json([
                'cultivos' => $cultivos
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'error' => 'Error del servidor',
                'mensaje' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtiene la tendencia de pedidos por mes
     */
    public function tendenciaPedidos(Request $request)
    {
        try {
            $meses = $request->meses ?? 6;
            
            // Obtener los últimos X meses
            $periodos = [];
            $labels = [];
            $ahora = Carbon::now();
            
            for ($i = $meses - 1; $i >= 0; $i--) {
                $fecha = $ahora->copy()->subMonths($i);
                $periodos[] = [
                    'inicio' => $fecha->copy()->startOfMonth()->format('Y-m-d'),
                    'fin' => $fecha->copy()->endOfMonth()->format('Y-m-d'),
                ];
                $labels[] = $fecha->format('M');
            }
            
            // Consultar pedidos por mes
            $datos = [];
            foreach ($periodos as $periodo) {
                $cantidad = Pedido::whereBetween('created_at', [$periodo['inicio'], $periodo['fin']])
                    ->count();
                $datos[] = $cantidad;
            }
            
            return response()->json([
                'tendencia_pedidos' => [
                    'labels' => $labels,
                    'data' => $datos
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'error' => 'Error del servidor',
                'mensaje' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtiene estadísticas generales para el dashboard
     */
    public function estadisticas(Request $request)
    {
        try {
            // Total de ventas (suma de pedidos)
            $totalVentas = Pedido::count();
            
            // Precio máximo de cultivos
            $precioMaximo = Cultivos::whereNotNull('precio_venta')
                ->max('precio_venta') ?? 0;
            
            // Total de categorías
            $totalCategorias = DB::table('categorias')
                ->where('estado', 1)
                ->count();
            
            // Total de cultivos
            $totalCultivos = Cultivos::count();
            
            return response()->json([
                'estadisticas' => [
                    'total_ventas' => $totalVentas,
                    'precio_maximo' => $precioMaximo,
                    'total_categorias' => $totalCategorias,
                    'total_cultivos' => $totalCultivos
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'error' => 'Error del servidor',
                'mensaje' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtiene todos los datos necesarios para el dashboard en una sola llamada
     */
    public function index(Request $request)
    {
        try {
            // Obtener cultivos caros
            $limit = $request->limit ?? 5;
            $cultivosCaros = Cultivos::whereNotNull('precio_venta')
                ->orderByDesc('precio_venta')
                ->with('cultivo_predefinido')
                ->limit($limit)
                ->get()
                ->map(function ($cultivo) {
                    return [
                        'id' => $cultivo->id,
                        'nombre' => $cultivo->cultivo_predefinido->nombre ?? 'Sin nombre',
                        'nombre_corto' => $cultivo->nombre_corto ?? $cultivo->cultivo_predefinido->nombre_corto ?? 'Sin nombre',
                        'precio_venta' => $cultivo->precio_venta,
                    ];
                });
            
            // Obtener tendencia de pedidos
            $meses = $request->meses ?? 6;
            $periodos = [];
            $labels = [];
            $ahora = Carbon::now();
            
            for ($i = $meses - 1; $i >= 0; $i--) {
                $fecha = $ahora->copy()->subMonths($i);
                $periodos[] = [
                    'inicio' => $fecha->copy()->startOfMonth()->format('Y-m-d'),
                    'fin' => $fecha->copy()->endOfMonth()->format('Y-m-d'),
                ];
                $labels[] = $fecha->format('M');
            }
            
            $datosPedidos = [];
            foreach ($periodos as $periodo) {
                $cantidad = Pedido::whereBetween('created_at', [$periodo['inicio'], $periodo['fin']])
                    ->count();
                $datosPedidos[] = $cantidad;
            }
            
            // Estadísticas generales
            $totalVentas = Pedido::count();
            $precioMaximo = Cultivos::whereNotNull('precio_venta')
                ->max('precio_venta') ?? 0;
            $totalCategorias = DB::table('categorias')
                ->where('estado', 1)
                ->count();
            $totalCultivos = Cultivos::count();
            
            return response()->json([
                'cultivos_caros' => [
                    'labels' => $cultivosCaros->pluck('nombre_corto')->toArray(),
                    'data' => $cultivosCaros->pluck('precio_venta')->toArray()
                ],
                'tendencia_pedidos' => [
                    'labels' => $labels,
                    'data' => $datosPedidos
                ],
                'estadisticas' => [
                    'total_ventas' => $totalVentas,
                    'precio_maximo' => $precioMaximo,
                    'total_categorias' => $totalCategorias,
                    'total_cultivos' => $totalCultivos
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'error' => 'Error del servidor',
                'mensaje' => $th->getMessage()
            ], 500);
        }
    }
}
