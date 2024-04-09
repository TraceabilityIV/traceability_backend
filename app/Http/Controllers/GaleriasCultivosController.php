<?php

namespace App\Http\Controllers;

use App\Models\Galeria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GaleriasCultivosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $galerias = Galeria::when($request->cultivo_id, function ($query) use ($request) {
            $query->where('cultivo_id', $request->cultivo_id);
        })
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "galerias" => $galerias
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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $campos = $request->only('cultivo_id');

            $galerias = [];

            if($request->hasFile('galeria')){
                $campos['nombre'] = $request->file('galeria')->hashName();
                $request->file('galeria')->storeAs('public/cultivos/galerias', $campos['nombre']);
                $campos['url'] = url('storage/cultivos/galerias/' . $campos['nombre']);
                $campos['nombre'] = $request->file('galeria')->getClientOriginalName();
                $campos['tipo'] = $this->obtenerTipoArchivo($request->file('galeria')->getClientMimeType());

                $galeria = Galeria::create($campos);
            }else if($request->hasFile('galerias')){
                foreach ($request->file('galerias') as $adjunto) {

                    $campos_adjuntos = [
                        'nombre' => $adjunto->hashName(),
                        'tipo' => $this->obtenerTipoArchivo($adjunto->getClientMimeType()),
                        'cultivo_id' => $request->cultivo_id,
                    ];

                    $adjunto->storeAs('public/cultivos/galerias/', $campos_adjuntos['nombre']);
                    $campos_adjuntos['url'] = url('storage/cultivos/galerias/' . $campos_adjuntos['nombre']);

                    $campos_adjuntos['nombre'] = $adjunto->getClientOriginalName();

                    $galerias[] = Galeria::create($campos_adjuntos);
                }
            }

            DB::commit();
            return response()->json([
                "galeria" => $galeria ?? $galerias,
                "mensaje" => "Galeria creada correctamente"
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $galeria = Galeria::find($id);

            if($galeria == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Galeria",
                ], 404);
            }

            $galeria->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Galeria eliminada correctamente"
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
