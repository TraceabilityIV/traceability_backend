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

        $cultivos = Cultivos::when(!auth()->user()->hasRole('Administrador'), function ($query){
            $query->where('usuario_id', auth()->user()->id);
        })
        ->with([
            'usuario',
        ])
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "cultivos" => $cultivos
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
                'nombre',
                'estado',
                'ubicacion',
                'direccion',
                'latitud',
                'longitud',
                'fecha_siembra',
                'area',
                'variedad',
                'nombre_corto',
                'lote',
                'prefijo_registro',
                'fecha_cosecha',
                'cantidad_aproximada',
                'usuario_id',
                'categoria_id'
            ]);

            $comision = Cultivos::create($campos);

            // $comision->categorias()->attach($request->categorias ?? []);
            // $comision->tipo_precios()->attach($request->tipo_precios ?? []);

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
            'categoria'
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
                'nombre',
                'estado',
                'ubicacion',
                'direccion',
                'latitud',
                'longitud',
                'fecha_siembra',
                'area',
                'variedad',
                'nombre_corto',
                'lote',
                'prefijo_registro',
                'fecha_cosecha',
                'cantidad_aproximada',
                'usuario_id',
                'categoria_id'
            ]);

            $cultivo->update($campos);

            // $cultivo->categorias()->sync($request->categorias ?? []);
            // $cultivo->tipo_precios()->sync($request->tipo_precios ?? []);

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
}
