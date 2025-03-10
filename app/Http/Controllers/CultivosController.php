<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cultivos\ActualizarRequest;
use App\Http\Requests\Cultivos\CrearRequest;
use App\Models\Categoria;
use App\Models\Cultivos;
use App\Models\CultivosFavorito;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Constraint\IsFalse;

class CultivosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cultivos = Cultivos::when(!auth()->user()->hasRole('Administrador'), function ($query) {
                $query->where('usuario_id', auth()->user()->id);
            })
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $query->whereHas('cultivo_predefinido', function ($query) use ($request) {
					$query->where('nombre', 'like', '%' . $request->buscar . '%');
				});
            })
            ->with(['usuario', 'cultivo_predefinido'])
            ->paginate($request->paginacion ?? 10);

        return response()->json([
            "cultivos" => $cultivos
        ]);
    }

    public function productos(Request $request){
        $productos = Cultivos::
            with([
                'usuario',
                'imagen',
                'precio',
				'cultivo_predefinido'
            ])
            ->when($request->buscar, function ($query) use ($request) {
                    $query->whereHas('cultivo_predefinido', function ($query) use ($request) {
						$query->where('nombre', 'like', "%{$request->buscar}%");
					})
                    ->orwhere('direccion', 'like', "%{$request->buscar}%")
                    ->orwhere('nombre_corto', 'like', "%{$request->buscar}%");
            })
            ->when($request->categoria_id, function ($query) use ($request) {
                    // $query->where('categoria_id', $request->categoria_id);
					$query->whereHas('cultivo_predefinido', function ($query) use ($request) {
						$query->where('categoria_id', $request->categoria_id);
					});
            })
            ->whereNull('pedido_id')
            ->when($request->latitud && $request->longitud && $request->radio, function ($query) use ($request) {
                $latitud = $request->latitud;
                $longitud = $request->longitud;
                $radio = $request->radio;

                $query->whereRaw("(
                    6371 * acos(
                        cos(radians(CAST(? AS double precision)))
                        * cos(radians(CAST(latitud AS double precision)))
                        * cos(radians(CAST(longitud AS double precision)) - radians(CAST(? AS double precision)))
                        + sin(radians(CAST(? AS double precision)))
                        * sin(radians(CAST(latitud AS double precision)))
                    )
                ) <= ?", [$latitud, $longitud, $latitud, $radio]);
            })
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "productos" => $productos
        ]);
    }

    public function productos_mapa(Request $request){
        logger($request);
        $productos = Cultivos::with(['imagen', 'precio', 'cultivo_predefinido'])
            ->whereNull('pedido_id')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->when($request->latitud && $request->longitud && $request->radio, function ($query) use ($request) {
                $latitud = $request->latitud;
                $longitud = $request->longitud;
                $radio = $request->radio;

                $query->whereRaw("(
                    6371 * acos(
                        cos(radians(CAST(? AS double precision)))
                        * cos(radians(CAST(latitud AS double precision)))
                        * cos(radians(CAST(longitud AS double precision)) - radians(CAST(? AS double precision)))
                        + sin(radians(CAST(? AS double precision)))
                        * sin(radians(CAST(latitud AS double precision)))
                    )
                ) <= ?", [$latitud, $longitud, $latitud, $radio]);
            })
            ->get();

        return response()->json([
            "productos" => $productos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CrearRequest $request)
    {
        DB::beginTransaction();
        try {
            $campos = $request->only([
                // 'nombre',
                'estado',
                'ubicacion',
                'direccion',
                'latitud',
                'longitud',
                'fecha_siembra',
                'area',
                'variedad',
                'lote',
                'prefijo_registro',
                'fecha_cosecha',
                'cantidad_aproximada',
                'usuario_id',
                'precio_venta',
				'cultivo_predefinido_id'
            ]);

            $comision = Cultivos::create($campos);

            DB::commit();
            return response()->json([
                "comision" => $comision,
                "mensaje" => "Cultivo creado correctamente"
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            Log::error($th);
            return response()->json([
                "error" => "Error del servidor",
                "mensaje" => $th->getMessage()
            ]);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cultivo = Cultivos::with([
            'usuario',
            'categoria',
			'cultivo_predefinido'
        ])->find($id);

        if($cultivo == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el Cultivo",
            ], 404);
        }

        return response()->json([
            "cultivo" => $cultivo
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActualizarRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $cultivo = Cultivos::find($id);

            if($cultivo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Cultivo",
                ], 404);
            }

            $campos = $request->only([
                // 'nombre',
                'estado',
                'ubicacion',
                'direccion',
                'latitud',
                'longitud',
                'fecha_siembra',
                'area',
                'variedad',
                'lote',
                'prefijo_registro',
                'fecha_cosecha',
                'cantidad_aproximada',
                'usuario_id',
                'precio_venta',
				'cultivo_predefinido_id'
            ]);

            $cultivo->update($campos);

            DB::commit();
            return response()->json([
                "cultivo" => $cultivo,
                "mensaje" => "Cultivo actualizado correctamente"
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollBack();

            return response()->json([
                "error" => "Error del servidor",
                "mensaje" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $cultivo = Cultivos::find($id);

            if($cultivo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Cultivo",
                ], 404);
            }

            $cultivo->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Cultivo eliminado correctamente"
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollBack();

            return response()->json([
                "error" => "Error del servidor",
                "mensaje" => $th->getMessage(),
            ], 500);
        }
    }

    public function favorito(string $id, Request $request){
        DB::beginTransaction();
        try {
            $cultivo = Cultivos::find($id);

            if($cultivo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Cultivo",
                ], 404);
            }

            $usuario = User::find(auth()->user()->id);

            $usuario->cultivos_favoritos()->sync([$cultivo->id], false);

            DB::commit();
            return response()->json([
                "mensaje" => "Cultivo agregado a Fovoritos"
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollBack();

            return response()->json([
                "error" => "Error del servidor",
                "mensaje" => $th->getMessage(),
            ], 500);
        }
    }

    public function favoritos(Request $request){
        $cultivos = User::find(auth()->user()->id)->cultivos_favoritos()->paginate($request->paginacion ?? 10);

        return response()->json([
            "cultivos" => $cultivos
        ]);
    }

    public function destroyFavorito(string $id){
        DB::beginTransaction();
        try {
            $cultivo = Cultivos::find($id);

            if($cultivo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Cultivo",
                ], 404);
            }

            $usuario = User::find(auth()->user()->id);

            $usuario->cultivos_favoritos()->detach($cultivo->id);

            DB::commit();
            return response()->json([
                "mensaje" => "Cultivo eliminado de Fovoritos"
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollBack();

            return response()->json([
                "error" => "Error del servidor",
                "mensaje" => $th->getMessage(),
            ], 500);
        }
    }


    public function categorias(Request $request){
        $categorias = Categoria::when($request->busca, function($query) use ($request){
            $query->where('nombre', 'like', '%' . $request->busca . '%');
        })
        ->where('estado', 1)
        ->paginate(10);

        return response()->json([
            "categorias" => $categorias
        ]);
    }

    public function usuarios(Request $request){
        $usuarios = User::select('users.*', DB::raw("CONCAT(nombres, ' ', apellidos) AS nombre_completo"))
        ->when($request->busca, function ($query) use ($request) {
            $query->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%{$request->busca}%"]);
        })
        ->whereHas('roles', function ($query) {
            $query->where('name', 'Vendedor');
        })
        ->where('estado', 1)
        ->paginate(10);

        return response()->json([
            "usuarios" => $usuarios
        ]);
    }

    public function detalle(string $id)
    {
        $producto = Cultivos::with([
            'usuario',
            'categoria',
            'galeria',
            'trazabilidad',
			'cultivo_predefinido'
        ])->find($id);

        if($producto == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el Producto",
            ], 404);
        }

        return response()->json([
            "producto" => $producto
        ]);
    }

    public function distribucionPrecios(Request $request)
    {
        $cultivos = Cultivos::where('estado', 1)
        ->where('usuario_id', auth()->user()->id)
        ->whereNull('pedido_id')
        ->get();

        return response()->json([
            "cultivos" => $cultivos
        ]);
    }
}
