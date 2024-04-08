<?php

namespace App\Http\Controllers;

use App\Http\Requests\Agrupadores\ActualizarRequest;
use App\Http\Requests\Agrupadores\CrearRequest;
use App\Models\Agrupador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgrupadoresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $agrupadores = Agrupador::paginate($request->paginacion ?? 10);

        return response()->json([
            "agrupadores" => $agrupadores
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
            $campos = $request->only('nombre', 'codigo', 'estado');

            $agrupador = Agrupador::create($campos);

            DB::commit();
            return response()->json([
                "agrupador" => $agrupador,
                "mensaje" => "Agrupador creado correctamente"
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
            $agrupador = Agrupador::find($id);

            if($agrupador == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Agrupador",
                ], 404);
            }

            $campos = $request->only('nombre', 'codigo', 'estado');

            $agrupador->update($campos);

            DB::commit();
            return response()->json([
                "agrupador" => $agrupador,
                "mensaje" => "Agrupador actualizado correctamente"
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
            $agrupador = Agrupador::find($id);

            if($agrupador == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Agrupador",
                ], 404);
            }

            $agrupador->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Agrupador eliminado correctamente"
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
