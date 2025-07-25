<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Paises\ActualizarRequest;
use App\Http\Requests\Paises\CrearRequest;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaisesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $paises = Pais::when($request->filled('busca'), function($query) use ($request){
            $query->where('nombre', 'like', '%' . $request->busca . '%')
			->orWhere('nombre_corto', 'like', '%' . $request->busca . '%')
			->orWhere('indicador', 'like', '%' . $request->busca . '%')
			->orWhere('codigo_postal', 'like', '%' . $request->busca . '%');
        })
		->when(!$request->filled('alls_fields'), function($query) use ($request){
            $query->where('estado', 1);
        })
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "paises" => $paises
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CrearRequest $request)
    {
        DB::beginTransaction();
        try {
            $campos = $request->only('nombre', 'nombre_corto', 'indicador', 'codigo_postal', 'estado');

            //subimos la bandera si hay
            if($request->hasFile('bandera')){
                $campos['bandera'] = $request->file('bandera')->hashName();
                $request->file('bandera')->storeAs('public/paises/banderas', $campos['bandera']);
                $campos['bandera'] = url('storage/paises/banderas/' . $campos['bandera']);
            }

            $pais = Pais::create($campos);

            DB::commit();
            return response()->json([
                "pais" => $pais,
                "mensaje" => "Pais creado correctamente"
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
        $pais = Pais::find($id);

        if($pais == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el País",
            ], 404);
        }

        return response()->json([
            "pais" => $pais
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
            $pais = Pais::find($id);

            if($pais == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el País",
                ], 404);
            }

            $campos = $request->only('nombre', 'nombre_corto', 'indicador', 'codigo_postal', 'estado');

            //subimos la bandera si hay
            if($request->hasFile('bandera')){
                $campos['bandera'] = $request->file('bandera')->hashName();
                $request->file('bandera')->storeAs('public/paises/banderas', $campos['bandera']);
                $campos['bandera'] = url('storage/paises/banderas/' . $campos['bandera']);
            }

            $pais->update($campos);

            DB::commit();
            return response()->json([
                "pais" => $pais,
                "mensaje" => "País actualizado correctamente"
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
            $pais = Pais::find($id);

            if($pais == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el País",
                ], 404);
            }

            $pais->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Pais eliminado correctamente"
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
