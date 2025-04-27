<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ciudades\ActualizarRequest;
use App\Http\Requests\Ciudades\CrearRequest;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CiudadesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ciudades = Ciudad::
        when($request->busca, function($query) use ($request){
            $query->where('nombre', 'like', '%' . $request->busca . '%')
				->orWhere('nombre_corto', 'like', '%' . $request->busca . '%')
				->orWhere('indicador', 'like', '%' . $request->busca . '%')
				->orWhere('codigo_postal', 'like', '%' . $request->busca . '%')
				->orWhereHas('departamento', function($query) use ($request) {
					$query->where('nombre', 'like', '%' . $request->busca . '%');
				});
        })
		->when($request->filled('departamento_id'), function($query) use ($request){
            $query->where('departamento_id', $request->departamento_id);
        })
		->when(!$request->filled('todas'), function($query) use ($request){
            $query->where('estado', 1);
        })
		->with([
			'departamento.pais'
		])
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "ciudades" => $ciudades
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
            $campos = $request->only('nombre', 'nombre_corto', 'indicador', 'codigo_postal', 'estado', 'departamento_id');

            //subimos la bandera si hay
            if($request->hasFile('bandera')){
                $campos['bandera'] = $request->file('bandera')->hashName();
                $request->file('bandera')->storeAs('public/ciudades/banderas', $campos['bandera']);
                $campos['bandera'] = url('storage/ciudades/banderas/' . $campos['bandera']);
            }

            $ciudad = Ciudad::create($campos);

            DB::commit();
            return response()->json([
                "ciudad" => $ciudad,
                "mensaje" => "Ciudad creada correctamente"
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
        $ciudad = Ciudad::with('departamento.pais')->find($id);

        if($ciudad == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro la Ciudad",
            ], 404);
        }

        return response()->json([
            "ciudad" => $ciudad
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
            $ciudad = Ciudad::find($id);

            if($ciudad == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Ciudad",
                ], 404);
            }

            $campos = $request->only('nombre', 'nombre_corto', 'indicador', 'codigo_postal', 'estado', 'departamento_id');

            //subimos la bandera si hay
            if($request->hasFile('bandera')){
                $campos['bandera'] = $request->file('bandera')->hashName();
                $request->file('bandera')->storeAs('public/ciudades/banderas', $campos['bandera']);
                $campos['bandera'] = url('storage/ciudades/banderas/' . $campos['bandera']);
            }

            $ciudad->update($campos);

            DB::commit();
            return response()->json([
                "ciudad" => $ciudad,
                "mensaje" => "Ciudad actualizada correctamente"
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
            $ciudad = Ciudad::find($id);

            if($ciudad == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Ciudad",
                ], 404);
            }

            $ciudad->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Ciudad eliminada correctamente"
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
        $ciudades = Ciudad::join('departamentos', 'departamentos.id', '=', 'ciudades.departamento_id')
        ->join('paises', 'paises.id', '=', 'departamentos.pais_id')
        ->select('ciudades.*', DB::raw("CONCAT(paises.nombre, ', ', departamentos.nombre, ', ', ciudades.nombre) AS nombre_completo"))
        ->when($request->busca, function ($query) use ($request) {
            $query->whereRaw("CONCAT(paises.nombre, ', ', departamentos.nombre, ', ', ciudades.nombre) LIKE ?", ["%{$request->busca}%"]);
        })
        ->paginate(20);

        return response()->json([
            "ciudades" => $ciudades
        ]);
    }
}
