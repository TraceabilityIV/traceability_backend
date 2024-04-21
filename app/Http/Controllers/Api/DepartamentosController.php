<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Departamentos\ActualizarRequest;
use App\Http\Requests\Departamentos\CrearRequest;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartamentosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $departamentos = Departamento::
        when($request->busca, function($query) use ($request){
            $query->where('nombre', 'like', '%' . $request->busca . '%');
        })
        ->where('pais_id', $request->pais_id)
        ->where('estado', 1)
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "departamentos" => $departamentos
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
            $campos = $request->only('nombre', 'nombre_corto', 'indicador', 'codigo_postal', 'estado', 'pais_id');

            //subimos la bandera si hay
            if($request->hasFile('bandera')){
                $campos['bandera'] = $request->file('bandera')->hashName();
                $request->file('bandera')->storeAs('public/departamentos/banderas', $campos['bandera']);
                $campos['bandera'] = url('storage/departamentos/banderas/' . $campos['bandera']);
            }

            $departamento = Departamento::create($campos);

            DB::commit();
            return response()->json([
                "departamento" => $departamento,
                "mensaje" => "Departamento creado correctamente"
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
            $departamento = Departamento::find($id);

            if($departamento == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Departamento",
                ], 404);
            }

            $campos = $request->only('nombre', 'nombre_corto', 'indicador', 'codigo_postal', 'estado', 'pais_id');

            //subimos la bandera si hay
            if($request->hasFile('bandera')){
                $campos['bandera'] = $request->file('bandera')->hashName();
                $request->file('bandera')->storeAs('public/departamentos/banderas', $campos['bandera']);
                $campos['bandera'] = url('storage/departamentos/banderas/' . $campos['bandera']);
            }

            $departamento->update($campos);

            DB::commit();
            return response()->json([
                "departamento" => $departamento,
                "mensaje" => "Departamento actualizado correctamente"
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
            $departamento = Departamento::find($id);

            if($departamento == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Departamento",
                ], 404);
            }

            $departamento->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Departamento eliminado correctamente"
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
