<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pqrs\CrearRequest;
use App\Models\AdjuntosPqr;
use App\Models\Pqr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PqrsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pqrs = Pqr::paginate($request->paginacion ?? 10);

        return response()->json([
            "pqrs" => $pqrs
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
            $campos = $request->only(
                'nombres',
                'correo',
                'telefono',
                'direccion',
                'asunto',
                'descripcion',
                'usuario_id',
                'barrio_id',
            );

            $pqr = Pqr::create($campos);

            //subimos varios archivos si existen
            if($request->hasFile('adjuntos')){
                foreach ($request->file('adjuntos') as $adjunto) {
                    $campos_adjuntos = [
                        'nombre' => $adjunto->hashName(),
                        'tipo' => $this->obtenerTipoArchivo($adjunto->getClientMimeType()),
                        'pqrs_id' => $pqr->id,
                    ];

                    $adjunto->storeAs('public/pqrs/', $campos_adjuntos['nombre']);
                    $campos_adjuntos['url'] = url('storage/pqrs/' . $campos_adjuntos['nombre']);

                    AdjuntosPqr::create($campos_adjuntos);
                }
            }

            DB::commit();
            return response()->json([
                "pqr" => $pqr,
                "mensaje" => "Pqr generado correctamente"
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
    public function show(string $id): JsonResponse
    {
        $pqr = Pqr::find($id);
        if ($pqr == null) {
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el PQR",
            ], 404);
        }
        return response()->json([
            "pqr" => $pqr,
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
