<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trazabilidades\ActualizarRequest;
use App\Http\Requests\Trazabilidades\CrearRequest;
use App\Models\TrazabilidadTransporte;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrazabilidadTransportesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $trazabilidad = TrazabilidadTransporte::when($request->pedido_id, function ($query) use ($request) {
            $query->where('pedido_id', $request->pedido_id);
        })
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "trazabilidad" => $trazabilidad
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
            $campos = $request->only('descripcion', 'observaciones', 'flag_entregado', 'pedido_id');

            $campos['usuario_id'] = auth()->user()->id;
            $campos['fecha'] = Carbon::now();

            $trazabilidad = TrazabilidadTransporte::create($campos);

            DB::commit();
            return response()->json([
                "trazabilidad" => $trazabilidad,
                "mensaje" => "Trazabilidad creada correctamente"
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
        //
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
            $trazabilidad = TrazabilidadTransporte::find($id);

            if($trazabilidad == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Trazabilidad",
                ], 404);
            }

            $campos = $request->only('descripcion', 'observaciones', 'flag_entregado');

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
            $trazabilidad = TrazabilidadTransporte::find($id);

            if($trazabilidad == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Trazabilidad",
                ], 404);
            }

            $trazabilidad->delete();

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
