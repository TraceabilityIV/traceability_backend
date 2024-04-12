<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estados\CrearRequest;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EstadosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $estados = Estado::paginate($request->paginacion ?? 10);

        return response()->json([
            "estados" => $estados
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
            $campos = $request->only('nombre', 'estado', 'flag_inicial', 'flag_final', 'estado_siguiente_id');

            if($request->hasFile('icono')){
                $campos['icono'] = $request->file('icono')->hashName();
                $request->file('icono')->storeAs('public/estados/', $campos['icono']);
                $campos['icono'] = url('storage/estados/' . $campos['icono']);
            }

            if($request->hasFile('icono_cumplido')){
                $campos['icono_cumplido'] = $request->file('icono_cumplido')->hashName();
                $request->file('icono_cumplido')->storeAs('public/estados/', $campos['icono_cumplido']);
                $campos['icono_cumplido'] = url('storage/estados/' . $campos['icono_cumplido']);
            }

            $estado = Estado::create($campos);

            DB::commit();
            return response()->json([
                "estado" => $estado,
                "mensaje" => "Estado creado correctamente"
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
        DB::beginTransaction();
        try {
            $estado = Estado::find($id);

            if($estado == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Estado",
                ], 404);
            }

            $campos = $request->only('nombre', 'estado', 'flag_inicial', 'flag_final', 'estado_siguiente_id');

            if($request->hasFile('icono')){
                $campos['icono'] = $request->file('icono')->hashName();
                $request->file('icono')->storeAs('public/estados/', $campos['icono']);
                $campos['icono'] = url('storage/estados/' . $campos['icono']);
            }

            if($request->hasFile('icono_cumplido')){
                $campos['icono_cumplido'] = $request->file('icono_cumplido')->hashName();
                $request->file('icono_cumplido')->storeAs('public/estados/', $campos['icono_cumplido']);
                $campos['icono_cumplido'] = url('storage/estados/' . $campos['icono_cumplido']);
            }

            $estado->update($campos);

            DB::commit();
            return response()->json([
                "estado" => $estado,
                "mensaje" => "Estado actualizado correctamente"
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
            $estado = Estado::find($id);

            if($estado == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Estado",
                ], 404);
            }

            $estado->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Estado eliminado correctamente"
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
