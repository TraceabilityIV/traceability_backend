<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Barrios\ActualizarRequest;
use App\Http\Requests\Barrios\CrearRequest;
use App\Models\Barrio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarriosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $barrios = Barrio::
        when($request->busca, function($query) use ($request){
            $query->where('nombre', 'like', '%' . $request->busca . '%')
			->orWhere('nombre_corto', 'like', '%' . $request->busca . '%')
			->orWhere('codigo_postal', 'like', '%' . $request->busca . '%')
			->orWhereHas('ciudad', function($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->busca . '%');
            });
        })
		->when($request->filled('ciudad_id'), function($query) use ($request){
            $query->where('ciudad_id', $request->ciudad_id);
        })
		->when(!$request->filled('todas'), function($query) use ($request){
            $query->where('estado', 1);
        })
        ->with([
            'ciudad'
        ])
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "barrios" => $barrios
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
            $campos = $request->only('nombre', 'nombre_corto', 'codigo_postal', 'estado', 'ciudad_id');

            $barrio = Barrio::create($campos);

            DB::commit();
            return response()->json([
                "barrio" => $barrio,
                "mensaje" => "Barrio creado correctamente"
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
        $barrio = Barrio::with('ciudad.departamento.pais')->find($id);

        if($barrio == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el Barrio",
            ], 404);
        }

        return response()->json([
            "barrio" => $barrio
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
            $barrio = Barrio::find($id);

            if($barrio == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Barrio",
                ], 404);
            }

            $campos = $request->only('nombre', 'nombre_corto', 'codigo_postal', 'estado', 'ciudad_id');

            $barrio->update($campos);

            DB::commit();
            return response()->json([
                "barrio" => $barrio,
                "mensaje" => "Barrio actualizado correctamente"
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
            $barrio = Barrio::find($id);

            if($barrio == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Barrio",
                ], 404);
            }

            $barrio->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Barrio eliminado correctamente"
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

    function directo(Request $request){
        $barrios = Barrio::join('ciudades', 'ciudades.id', '=', 'barrios.ciudad_id')
        ->join('departamentos', 'departamentos.id', '=', 'ciudades.departamento_id')
        ->join('paises', 'paises.id', '=', 'departamentos.pais_id')
        ->select('barrios.*', DB::raw("CONCAT(paises.nombre, ', ', departamentos.nombre, ', ', ciudades.nombre, ', ', barrios.nombre) AS nombre_completo"))
        ->when($request->busca, function ($query) use ($request) {
            $query->whereRaw("CONCAT(paises.nombre, ', ', departamentos.nombre, ', ', ciudades.nombre, ', ', barrios.nombre) LIKE ?", ["%{$request->busca}%"]);
        })
        ->paginate(20);

        return response()->json([
            "barrios" => $barrios
        ]);
    }
}
