<?php

namespace App\Http\Controllers;

use App\Http\Requests\Trazabilidad\ActualizarRequest;
use App\Http\Requests\Trazabilidad\CrearRequest;
use App\Jobs\ResumenTrazabilidadCultivosJob;
use App\Models\Cultivos;
use App\Models\TrazabilidadCultivo;
use App\Services\DeepseekService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrazabilidadCultivosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $trazabilidades = TrazabilidadCultivo::when($request->cultivo_id, function ($query) use ($request) {
            $query->where('cultivo_id', $request->cultivo_id);
        })
        ->when($request->filled('buscar'), function ($query) use ($request) {
            $query->where('aplicacion', 'like', "%{$request->buscar}%")
            ->orWhere('descripcion', 'like', "%{$request->buscar}%")
            ->orWhere('resultados', 'like', "%{$request->buscar}%");
        })
        ->orderBy('fecha_aplicacion', 'DESC')
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "trazabilidades" => $trazabilidades
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
            $campos = $request->only('cultivo_id', 'aplicacion', 'descripcion', 'resultados', 'fecha_aplicacion', 'ultima_revision');

            $campos['usuario_id'] = auth()->user()->id;

            if(!isset($campos['ultima_revision'])){
                $campos['ultima_revision'] = Carbon::now()->format('Y-m-d');
            }

            if(!isset($campos['fecha_aplicacion'])){
                $campos['fecha_aplicacion'] = Carbon::now()->format('Y-m-d');
            }

            $trazabilidad = TrazabilidadCultivo::create($campos);

			ResumenTrazabilidadCultivosJob::dispatch($trazabilidad->cultivo_id);

            DB::commit();
            return response()->json([
                "trazabilidad" => $trazabilidad,
                "mensaje" => "Comisión creada correctamente"
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
        $trazabilidad = TrazabilidadCultivo::with([
            'cultivo'
        ])->find($id);

        if($trazabilidad == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro la Trazabilidad",
            ], 404);
        }

        return response()->json([
            "trazabilidad" => $trazabilidad
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActualizarRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $trazabilidad = TrazabilidadCultivo::find($id);

            if($trazabilidad == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Trazabilidad",
                ], 404);
            }

            $campos = $request->only('aplicacion', 'descripcion', 'resultados', 'ultima_revision', 'fecha_aplicacion');

            if(!isset($campos['ultima_revision']) && isset($campos['fecha_aplicacion'])){
                $campos['ultima_revision'] = $campos['fecha_aplicacion'];
            }

            $trazabilidad->update($campos);

			ResumenTrazabilidadCultivosJob::dispatch($trazabilidad->cultivo_id);

            DB::commit();
            return response()->json([
                "trazabilidad" => $trazabilidad,
                "mensaje" => "Trazabilidad actualizada correctamente"
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
            $trazabilidd = TrazabilidadCultivo::find($id);

            if($trazabilidd == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Trazabilidad",
                ], 404);
            }

            $trazabilidd->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Trazabilidad eliminada correctamente"
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

	public function resumen(Request $request){
		$trazabilidades = TrazabilidadCultivo::when($request->cultivo_id, function ($query) use ($request) {
            $query->where('cultivo_id', $request->cultivo_id);
        })
		->with([
			'evidencias' => function($query) {
				$query->select('id', 'trazabilidad_cultivos_id', 'nombre', 'descripcion');
			}
		])
		->select('id', 'aplicacion', 'descripcion', 'resultados', 'fecha_aplicacion', 'ultima_revision', 'cultivo_id')
		->get();

		$res = app(DeepseekService::class)->resumenTrazabilidad(json_encode($trazabilidades));

		return response()->json([
			"resumen" => $res['choices'][0]['message']['content'] ?? ""
		]);
	}

	public function convertJsonIA($res){
		$json_string = $res["choices"][0]['message']['content'] ?? ""; 

		// Decodificar el JSON (asegurarse de que sea un array asociativo)
		$data = json_decode($json_string, true);

		if (json_last_error() === JSON_ERROR_NONE) {
			// echo "JSON válido:\n";
			// print_r($data); // Aquí ya es un array asociativo limpio
			// if (!empty($data['cultivos'])) {
			// 	$data['cultivos'] = array_map(function($item) {
			// 		$cultivo = CultivosPredefinidos::find($item['id'] ?? null);
			// 		return array_merge($item, ['cultivo' => $cultivo]);
			// 	}, $data['cultivos']);
			// }
		} else {
			$data = "Error al decodificar JSON: " . json_last_error_msg();
			// $data = $res;
		}

		return $data;
	}
}
