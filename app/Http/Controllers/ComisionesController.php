<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comisiones\ActualizarRequest;
use App\Http\Requests\Comisiones\CrearRequest;
use App\Models\Comision;
use App\Models\ComisionesHasCategorias;
use App\Models\Subagrupadores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComisionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $comisiones = Comision::withCount([
            'categorias',
            'tipo_precios',
        ])->paginate($request->paginacion ?? 10);

        return response()->json([
            "comisiones" => $comisiones
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
            $campos = $request->only('nombre', 'porcentaje', 'estado');

            $comision = Comision::create($campos);

            $comision->categorias()->attach($request->categorias ?? []);
            $comision->tipo_precios()->attach($request->tipo_precios ?? []);

            DB::commit();
            return response()->json([
                "comision" => $comision->load('categorias', 'tipo_precios'),
                "mensaje" => "Comisión creada correctamente"
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
        $comision = Comision::with([
            'categorias',
            'tipo_precios'
        ])->find($id);

        if($comision == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro la comisión",
            ], 404);
        }

        return response()->json([
            "comision" => $comision
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
            $comision = Comision::find($id);

            if($comision == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Comisión",
                ], 404);
            }

            $campos = $request->only('nombre', 'porcentaje', 'estado');

            $comision->update($campos);

            $comision->categorias()->sync($request->categorias ?? []);
            $comision->tipo_precios()->sync($request->tipo_precios ?? []);

            DB::commit();
            return response()->json([
                "comision" => $comision->load('categorias', 'tipo_precios'),
                "mensaje" => "Comisión actualizado correctamente"
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
            $comision = Comision::find($id);

            if($comision == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Comisión",
                ], 404);
            }

            $comision->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Comisión eliminada correctamente"
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

    public function tipos_precios(){
        $tipos_precios = Subagrupadores::whereHas('agrupador', function($query){
            $query->where('codigo', 'tipos_precios_ventas')->where('estado', 1);
        })
        ->where('estado', 1)
        ->get();

        return response()->json([
            "tipos_precios" => $tipos_precios
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
