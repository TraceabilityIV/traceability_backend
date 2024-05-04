<?php

namespace App\Http\Controllers;

use App\Http\Requests\Trazabilidad\ActualizarRequest;
use App\Http\Requests\Trazabilidad\CrearRequest;
use App\Models\Cultivos;
use App\Models\TrazabilidadCultivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrazabilidadCultivosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $trazabilidades = TrazabilidadCultivo::when($request->cultivo_id, function ($query) use ($request) {
            $query->where('cultivo_id', $request->cultivo_id);
        })
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "trazabilidades" => $trazabilidades
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
            $campos = $request->only('cultivo_id', 'aplicacion', 'descripcion', 'resultados');

            $campos['usuario_id'] = auth()->user()->id;

            $trazabilidad = TrazabilidadCultivo::create($campos);

            DB::commit();
            return response()->json([
                "trazabilidad" => $trazabilidad,
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
        $trazabilidad = TrazabilidadCultivo::with([
            'cultivo'
        ])->find($id);

        if($trazabilidad == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro la Trazabilidad",
            ], 404);
        }

        return response()->json([
            "trazabilidad" => $trazabilidad
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActualizarRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $trazabilidad = TrazabilidadCultivo::find($id);

            if($trazabilidad == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Trazabilidad",
                ], 404);
            }

            $campos = $request->only('aplicacion', 'descripcion', 'resultados');

            $trazabilidad->update($campos);

            DB::commit();
            return response()->json([
                "trazabilidad" => $trazabilidad,
                "mensaje" => "Trazabilidad actualizada correctamente"
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
            $trazabilidd = TrazabilidadCultivo::find($id);

            if($trazabilidd == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Trazabilidad",
                ], 404);
            }

            $trazabilidd->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Trazabilidad eliminada correctamente"
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
}
