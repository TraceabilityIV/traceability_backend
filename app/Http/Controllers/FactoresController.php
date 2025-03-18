<?php

namespace App\Http\Controllers;

use App\Http\Requests\Factores\CrearRequest;
use App\Models\Factores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FactoresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		$factores = Factores::
			when($request->filled('buscar'), function ($query) use ($request) {
				$query->where('nombre', 'like', '%' . $request->buscar . '%')
				->orWhere('descripcion', 'like', '%' . $request->buscar . '%')
				->orWhereHas('ciudad', function ($query) use ($request) {
					$query->where('nombre', 'like', '%' . $request->buscar . '%');
				});
			})
			->with(['ciudad'])
			->paginate($request->paginacion ?? 10);

		return response()->json([
			"factores" => $factores
		]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CrearRequest $request)
    {
		DB::beginTransaction();
        try {
            $campos = $request->only([
				'nombre',
				'descripcion',
				'peso',
				'fecha_inicio',
				'fecha_fin',
				'barrio_id',
				'ciudad_id',
				'departamento_id',
				'pais_id',
				'latitud',
				'longitud',
				'radio',
				'poligono',
            ]);

            $factor = Factores::create($campos);

			$factor->cultivos_predefinidos()->sync($request->cultivos ?? []);

            DB::commit();

            return response()->json([
                "factor" => $factor,
                "mensaje" => "Factor creado correctamente"
            ]);
        } catch (\Throwable $th) {
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
    public function show($id)
    {
		$factor = Factores::with([
            'ciudad', 'cultivos_predefinidos'
        ])->find($id);

        if($factor == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el Factor",
            ], 404);
        }

        return response()->json([
            "factor" => $factor
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CrearRequest $request, $id)
    {
		DB::beginTransaction();
        try {
            $factor = Factores::find($id);

            if($factor == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Factor",
                ], 404);
            }

            $campos = $request->only([
				'nombre',
				'descripcion',
				'peso',
				'fecha_inicio',
				'fecha_fin',
				'barrio_id',
				'ciudad_id',
				'departamento_id',
				'pais_id',
				'latitud',
				'longitud',
				'radio',
				'poligono',
            ]);

            $factor->update($campos);

			$factor->cultivos_predefinidos()->sync($request->cultivos ?? []);

            DB::commit();
            return response()->json([
                "factor" => $factor,
                "mensaje" => "Factor actualizado correctamente"
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
    public function destroy($id)
    {
        try {
            $factor = Factores::find($id);

            if($factor == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Factor",
                ], 404);
            }

            $factor->delete();

            return response()->json([
                "mensaje" => "Factor eliminado correctamente"
            ]);
        } catch (\Throwable $th) {
            Log::error($th);

            return response()->json([
                "error" => "Error del servidor",
                "mensaje" => $th->getMessage(),
            ], 500);
        } 
    }
}
