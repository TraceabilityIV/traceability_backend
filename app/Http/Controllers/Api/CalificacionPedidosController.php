<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calificaciones\ActualizarRequest;
use App\Http\Requests\Calificaciones\CrearRequest;
use App\Models\CalificacionPedido;
use App\Models\TrazabilidadTransporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalificacionPedidosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $calificaciones = CalificacionPedido::when($request->pedido_id, function ($query) use ($request) {
            $query->where('pedido_id', $request->pedido_id);
        })
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "calificaciones" => $calificaciones
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
            $campos = $request->only( 'pedido_id', 'calificacion', 'comentario', 'descripcion',);

            $campos['usuario_id'] = auth()->user()->id;

            $calificacion = CalificacionPedido::create($campos);

            DB::commit();
            return response()->json([
                "calificacion" => $calificacion,
                "mensaje" => "Calificación creada correctamente"
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
        try {
            $calificacion = CalificacionPedido::find($id);

            if($calificacion == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Calificación",
                ], 404);
            }

            $campos = $request->only( 'calificacion', 'comentario', 'descripcion',);

            $calificacion->update($campos);

            DB::commit();
            return response()->json([
                "calificacion" => $calificacion,
                "mensaje" => "Calificación actualizada correctamente"
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
            $calificacion = CalificacionPedido::find($id);

            if($calificacion == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Calificación",
                ], 404);
            }

            $calificacion->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Calificación eliminada correctamente"
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
