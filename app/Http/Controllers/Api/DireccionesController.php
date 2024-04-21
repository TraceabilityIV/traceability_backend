<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Direcciones\ActualizarRequest;
use App\Http\Requests\Direcciones\CrearRequest;
use App\Models\Direcciones;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DireccionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $direcciones = Direcciones::when($request->usuario_id, function ($query) use ($request){
            $query->where('usuario_id', $request->usuario_id);
        })
        ->unless($request->usuario_id, function ($query){
            $query->where('usuario_id', auth()->user()->id);
        })
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "direcciones" => $direcciones
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
            $campos = $request->only('direccion', 'receptor', 'latitud', 'longitud', 'barrio_id', 'usuario_id', 'estado');

            $direccion = Direcciones::create($campos);

            DB::commit();
            return response()->json([
                "direccion" => $direccion,
                "mensaje" => "Dirección creada correctamente"
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
            $direccion = Direcciones::find($id);

            if($direccion == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Dirección",
                ], 404);
            }

            $campos = $request->only('direccion', 'receptor', 'latitud', 'longitud', 'barrio_id', 'usuario_id', 'estado');

            $direccion->update($campos);

            DB::commit();
            return response()->json([
                "direccion" => $direccion,
                "mensaje" => "Dirección actualizada correctamente"
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
            $direccion = Direcciones::find($id);

            if($direccion == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Dirección",
                ], 404);
            }

            $direccion->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Dirección eliminada correctamente"
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
