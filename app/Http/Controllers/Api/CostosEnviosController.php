<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CostosEnvios\ActualizarRequest;
use App\Http\Requests\CostosEnvios\CrearRequest;
use App\Models\Categoria;
use App\Models\CostosEnvio;
use App\Models\Subagrupadores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CostosEnviosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $costos = CostosEnvio::with(['tipo_costo'])
        ->withCount('categorias')
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "costos" => $costos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CrearRequest $request)
    {
        DB::beginTransaction();
        try {
            $campos = $request->only('costo', 'estado', 'tipo_costo_id');

            $costo = CostosEnvio::create($campos);

            $costo->categorias()->attach($request->categorias ?? []);

            DB::commit();
            return response()->json([
                "costo" => $costo,
                "mensaje" => "Costo de envio creado correctamente"
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
        $costo = CostosEnvio::with([
            'tipo_costo',
            'categorias'
        ])->find($id);

        if($costo == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el Costo de envio",
            ], 404);
        }

        return response()->json([
            "costo" => $costo
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
            $costo = CostosEnvio::find($id);

            if($costo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Costo de envio",
                ], 404);
            }

            $campos = $request->only('costo', 'estado', 'tipo_costo_id');

            $costo->update($campos);

            $costo->categorias()->sync($request->categorias ?? []);

            DB::commit();
            return response()->json([
                "costo" => $costo,
                "mensaje" => "Costo de envio actualizado correctamente"
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
            $costo = CostosEnvio::find($id);

            if($costo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Costo de envio",
                ], 404);
            }

            $costo->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Costo de envio eliminado correctamente"
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

    public function tipos_costos(){
        $tipos_costos = Subagrupadores::whereHas('agrupador', function($query){
            $query->where('codigo', 'tipos_costos_envio')->where('estado', 1);
        })
        ->where('estado', 1)
        ->get();

        return response()->json([
            "tipos_costos" => $tipos_costos
        ]);
    }

    public function categorias(Request $request){
        $categorias = Categoria::when($request->busca, function($query) use ($request){
            $query->where('nombre', 'like', '%' . $request->busca . '%');
        })
        ->where('estado', 1)
        ->get();

        return response()->json([
            "categorias" => $categorias
        ]);
    }

    public function costo_categorias($id){
        $costo = CostosEnvio::with(['categorias'])->find($id);

        if($costo == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el Costo de envio",
            ], 404);
        }

        return response()->json([
            "categorias" => $costo->categorias
        ]);
    }
}
