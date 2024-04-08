<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subagrupadores\ActualizarRequest;
use App\Http\Requests\Subagrupadores\CrearRequest;
use App\Models\Subagrupadores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubagrupadoresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $subagrupadores = Subagrupadores::paginate($request->paginacion ?? 10);

        return response()->json([
            "subagrupadores" => $subagrupadores
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
            $campos = $request->only('nombre', 'codigo', 'estado', 'agrupador_id');

            $subagrupador = Subagrupadores::create($campos);

            DB::commit();
            return response()->json([
                "subagrupador" => $subagrupador,
                "mensaje" => "SubAgrupador creado correctamente"
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
            $subagrupador = Subagrupadores::find($id);

            if($subagrupador == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el SubAgrupador",
                ], 404);
            }

            $campos = $request->only('nombre', 'codigo', 'estado', 'agrupador_id');

            $subagrupador->update($campos);

            DB::commit();
            return response()->json([
                "subagrupador" => $subagrupador,
                "mensaje" => "SubAgrupador actualizado correctamente"
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
            $subagrupador = Subagrupadores::find($id);

            if($subagrupador == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Subagrupador",
                ], 404);
            }

            $subagrupador->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "SubAgrupador eliminado correctamente"
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
