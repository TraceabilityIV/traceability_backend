<?php

namespace App\Http\Controllers;

use App\Http\Requests\CultivosPredefinidos\CreateRequest;
use App\Models\CultivosPredefinidos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CultivosPredefinidosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		$cultivos = CultivosPredefinidos::
			when($request->filled('buscar'), function ($query) use ($request) {
				$query->where('nombre', 'like', '%' . $request->buscar . '%')
				->orWhere('nombre_corto', 'like', '%' . $request->buscar . '%')
				->orWHereHas('categoria', function ($query) use ($request) {
					$query->where('nombre', 'like', '%' . $request->buscar . '%');
				})
				->orWhere('textura_suelo', 'like', '%' . $request->buscar . '%')
				->orWhere('profundidad_suelo', 'like', '%' . $request->buscar . '%')
				->orWhere('dias_crecimiento', 'like', '%' . $request->buscar . '%')
				->orWhere('ph_max', 'like', '%' . $request->buscar . '%')
				->orWhere('ph_min', 'like', '%' . $request->buscar . '%')
				->orWhere('temperatura_max', 'like', '%' . $request->buscar . '%')
				->orWhere('temperatura_min', 'like', '%' . $request->buscar . '%');
			})
			->with(['categoria'])
			->paginate($request->paginacion ?? 10);

		return response()->json([
			"cultivos" => $cultivos
		]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
		DB::beginTransaction();
        try {
            $campos = $request->only([
                'nombre',
                'nombre_corto',
                'categoria_id',
				'temperatura_min',
				'temperatura_max',
                'ph_min',
                'ph_max',
                'dias_crecimiento',
                'profundidad_suelo',
                'textura_suelo'
            ]);

            if($request->hasFile('imagen')){
                $campos['imagen'] = $request->file('imagen')->hashName();
                $request->file('imagen')->storeAs('public/productos_predefinidos/', $campos['imagen']);
                $campos['imagen'] = url('storage/productos_predefinidos/' . $campos['imagen']);
            }


            $cultivo_predefinido = CultivosPredefinidos::create($campos);

            DB::commit();

            return response()->json([
                "cultivo_predefinido" => $cultivo_predefinido,
                "mensaje" => "Cultivo Predefinido creado correctamente"
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
		$cultivo_predefinido = CultivosPredefinidos::with([
            'categoria'
        ])->find($id);

        if($cultivo_predefinido == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el Cultivo Predefinido",
            ], 404);
        }

        return response()->json([
            "cultivo_predefinido" => $cultivo_predefinido
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateRequest $request, $id)
    {
		DB::beginTransaction();
        try {
            $cultivo_predefinido = CultivosPredefinidos::find($id);

            if($cultivo_predefinido == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Cultivo Predefinido",
                ], 404);
            }

            $campos = $request->only([
                'nombre',
                'nombre_corto',
                'categoria_id',
				'temperatura_min',
				'temperatura_max',
                'ph_min',
                'ph_max',
                'dias_crecimiento',
                'profundidad_suelo',
                'textura_suelo'
            ]);

			if($request->hasFile('imagen')){
                $campos['imagen'] = $request->file('imagen')->hashName();
                $request->file('imagen')->storeAs('public/productos_predefinidos/', $campos['imagen']);
                $campos['imagen'] = url('storage/productos_predefinidos/' . $campos['imagen']);
            }

            $cultivo_predefinido->update($campos);

            DB::commit();
            return response()->json([
                "cultivo_predefinido" => $cultivo_predefinido,
                "mensaje" => "Cultivo actualizado correctamente"
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
            $cultivo = CultivosPredefinidos::find($id);

            if($cultivo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Cultivo Predefinido",
                ], 404);
            }

            $cultivo->delete();

            return response()->json([
                "mensaje" => "Cultivo predefinido eliminado correctamente"
            ]);
        } catch (\Throwable $th) {
            Log::error($th);

            return response()->json([
                "error" => "Error del servidor",
                "mensaje" => $th->getMessage(),
            ], 500);
        }
    }

	public function externo(Request $request)
    {
		$cultivos = CultivosPredefinidos::
			when($request->filled('busca'), function ($query) use ($request) {
				$query->where('nombre', 'like', '%' . $request->busca . '%')
				->orWhere('nombre_corto', 'like', '%' . $request->busca . '%')
				->orWhere('textura_suelo', 'like', '%' . $request->busca . '%')
				->orWhere('profundidad_suelo', 'like', '%' . $request->busca . '%')
				->orWhere('dias_crecimiento', 'like', '%' . $request->busca . '%')
				->orWhere('ph_max', 'like', '%' . $request->busca . '%')
				->orWhere('ph_min', 'like', '%' . $request->busca . '%')
				->orWhere('temperatura_max', 'like', '%' . $request->busca . '%')
				->orWhere('temperatura_min', 'like', '%' . $request->busca . '%');
			})
			->paginate($request->paginacion ?? 10);
		return response()->json([
			"cultivos" => $cultivos
		]);
    }
}
