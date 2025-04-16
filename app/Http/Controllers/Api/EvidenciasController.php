<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Evidencias\ActualizarRequest;
use App\Http\Requests\Evidencias\CrearRequest;
use App\Http\Requests\Evidencias\ObtenerRequest;
use App\Jobs\ResumenTrazabilidadCultivosJob;
use App\Models\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvidenciasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ObtenerRequest $request)
    {
        $evidencias = Evidencia::where('trazabilidad_cultivos_id', $request->trazabilidad_cultivos_id)->paginate($request->paginacion ?? 10);

        return response()->json([
            "evidencias" => $evidencias
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
            $campos = $request->only('descripcion', 'trazabilidad_cultivos_id', 'evidencia');

            if($request->hasFile('evidencia')){
                $campos['nombre'] = $request->file('evidencia')->hashName();
                $request->file('evidencia')->storeAs('public/cultivos/evidencias', $campos['nombre']);
                $campos['url'] = url('storage/cultivos/evidencias/' . $campos['nombre']);
                $campos['nombre'] = $request->file('evidencia')->getClientOriginalName();
                $campos['tipo'] = $this->obtenerTipoArchivo($request->file('evidencia')->getClientMimeType());
            }

            $evidencia = Evidencia::create($campos);

            DB::commit();
            return response()->json([
                "comision" => $evidencia,
                "mensaje" => "Evidencia creada correctamente"
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
        $evidencia = Evidencia::with([
            'trazabilidad_cultivos.cultivo.cultivo_predefinido'
        ])->find($id);

        if($evidencia == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro la Evidencia",
            ], 404);
        }

        return response()->json([
            "evidencia" => $evidencia
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
            $evidencia = Evidencia::find($id);

            if($evidencia == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Evidencia",
                ], 404);
            }

            $campos = $request->only('descripcion', 'evidencia');

            if($request->hasFile('evidencia')){
                $campos['nombre'] = $request->file('evidencia')->hashName();
                $request->file('evidencia')->storeAs('public/cultivos/evidencias', $campos['nombre']);
                $campos['url'] = url('storage/cultivos/evidencias/' . $campos['nombre']);
                $campos['nombre'] = $request->file('evidencia')->getClientOriginalName();
                $campos['tipo'] = $this->obtenerTipoArchivo($request->file('evidencia')->getClientMimeType());
            }

            $evidencia->update($campos);

            DB::commit();
            return response()->json([
                "evidencia" => $evidencia,
                "mensaje" => "Evidencia actualizada correctamente"
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
            $evidencia = Evidencia::find($id);

            if($evidencia == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Evidencia",
                ], 404);
            }

            $evidencia->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Evidencia eliminada correctamente"
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

    public function obtenerTipoArchivo($mimeType){
        if (strpos($mimeType, 'image/') === 0) {
            return 'imagen';
        } elseif (strpos($mimeType, 'video/') === 0) {
            return 'video';
        } elseif (strpos($mimeType, 'audio/') === 0) {
            return 'audio';
        }

        return 'archivo';
    }
}
