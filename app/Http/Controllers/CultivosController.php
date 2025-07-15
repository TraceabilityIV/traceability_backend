<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cultivos\ActualizarRequest;
use App\Http\Requests\Cultivos\CrearRequest;
use App\Models\Categoria;
use App\Models\Ciudad;
use App\Models\Cultivos;
use App\Models\CultivosFavorito;
use App\Models\CultivosPredefinidos;
use App\Models\Factores;
use App\Models\User;
use App\Services\DeepseekService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Constraint\IsFalse;

class CultivosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cultivos = Cultivos::when(!auth()->user()->hasRole('Administrador'), function ($query) {
                $query->where('usuario_id', auth()->user()->id);
            })
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $query->whereHas('cultivo_predefinido', function ($query) use ($request) {
					$query->where('nombre', 'like', '%' . $request->buscar . '%');
				});
            })
            ->with(['usuario', 'cultivo_predefinido'])
            ->paginate($request->paginacion ?? 10);

        return response()->json([
            "cultivos" => $cultivos
        ]);
    }

    /**
     * Obtiene una lista paginada de productos (cultivos) con opciones de búsqueda y filtrado.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @queryParam buscar string Término de búsqueda para filtrar por nombre de cultivo, dirección o nombre corto.
     * @queryParam categoria_id integer ID de la categoría para filtrar los cultivos.
     * @queryParam latitud float Latitud de referencia para búsqueda por radio.
     * @queryParam longitud float Longitud de referencia para búsqueda por radio.
     * @queryParam radio float Radio en kilómetros para la búsqueda por ubicación.
     * @queryParam paginacion integer Número de elementos por página (opcional, por defecto 10).
     */
    public function productos(Request $request){
        $productos = Cultivos::
            with([
                'usuario',
                'imagen',
                'precio',
				'cultivo_predefinido'
            ])
            // Filtro de búsqueda: busca coincidencias en nombre del cultivo, dirección o nombre corto
            ->when($request->buscar, function ($query) use ($request) {
                $query->whereHas('cultivo_predefinido', function ($query) use ($request) {
                    $query->where('nombre', 'like', "%{$request->buscar}%");
                })
                ->orWhere('direccion', 'like', "%{$request->buscar}%")
                ->orWhere('nombre_corto', 'like', "%{$request->buscar}%");
            })
            // Filtro por categoría: solo muestra cultivos de la categoría especificada
            ->when($request->categoria_id, function ($query) use ($request) {
                $query->whereHas('cultivo_predefinido', function ($query) use ($request) {
                    $query->where('categoria_id', $request->categoria_id);
                });
            })
            ->whereNull('pedido_id')
			//Filtro por ubicación: solo muestra cultivos dentro del radio especificado
            ->when($request->latitud && $request->longitud && $request->radio, function ($query) use ($request) {
                $latitud = $request->latitud;
                $longitud = $request->longitud;
                $radio = $request->radio;

                $query->whereRaw("(
                    6371 * acos(
                        cos(radians(CAST(? AS double precision)))
                        * cos(radians(CAST(latitud AS double precision)))
                        * cos(radians(CAST(longitud AS double precision)) - radians(CAST(? AS double precision)))
                        + sin(radians(CAST(? AS double precision)))
                        * sin(radians(CAST(latitud AS double precision)))
                    )
                ) <= ?", [$latitud, $longitud, $latitud, $radio]);
            })
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "productos" => $productos
        ]);
    }

    /**
     * Obtiene productos para mostrar en un mapa, con filtros de ubicación.
     * Similar a productos() pero sin paginación y con campos optimizados para mapas.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @queryParam latitud float Latitud de referencia para búsqueda por radio (requerido con longitud y radio).
     * @queryParam longitud float Longitud de referencia para búsqueda por radio (requerido con latitud y radio).
     * @queryParam radio float Radio en kilómetros para la búsqueda por ubicación (requerido con latitud y longitud).
     */
    public function productos_mapa(Request $request){
        // Obtiene productos con datos mínimos necesarios para mostrar en un mapa
        $productos = Cultivos::with(['imagen', 'precio', 'cultivo_predefinido'])
            ->whereNull('pedido_id')  // Solo cultivos no asociados a pedidos
            ->whereNotNull('latitud')  // Solo cultivos con coordenadas válidas
            ->whereNotNull('longitud') // Solo cultivos con coordenadas válidas
            ->when($request->latitud && $request->longitud && $request->radio, function ($query) use ($request) {
                $latitud = $request->latitud;
                $longitud = $request->longitud;
                $radio = $request->radio;

                $query->whereRaw("(
                    6371 * acos(
                        cos(radians(CAST(? AS double precision)))
                        * cos(radians(CAST(latitud AS double precision)))
                        * cos(radians(CAST(longitud AS double precision)) - radians(CAST(? AS double precision)))
                        + sin(radians(CAST(? AS double precision)))
                        * sin(radians(CAST(latitud AS double precision)))
                    )
                ) <= ?", [$latitud, $longitud, $latitud, $radio]);
            })
            ->get();

        return response()->json([
            "productos" => $productos
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
            $campos = $request->only([
                // 'nombre',
                'estado',
                'ubicacion',
                'direccion',
                'latitud',
                'longitud',
                'fecha_siembra',
                'area',
                'variedad',
                'lote',
                'prefijo_registro',
                'fecha_cosecha',
                'cantidad_aproximada',
                'usuario_id',
                'precio_venta',
				'cultivo_predefinido_id'
            ]);

            $comision = Cultivos::create($campos);

            DB::commit();
            return response()->json([
                "comision" => $comision,
                "mensaje" => "Cultivo creado correctamente"
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
        $cultivo = Cultivos::with([
            'usuario',
            'categoria',
			'cultivo_predefinido'
        ])->find($id);

        if($cultivo == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el Cultivo",
            ], 404);
        }

        return response()->json([
            "cultivo" => $cultivo
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
            $cultivo = Cultivos::find($id);

            if($cultivo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Cultivo",
                ], 404);
            }

            $campos = $request->only([
                // 'nombre',
                'estado',
                'ubicacion',
                'direccion',
                'latitud',
                'longitud',
                'fecha_siembra',
                'area',
                'variedad',
                'lote',
                'prefijo_registro',
                'fecha_cosecha',
                'cantidad_aproximada',
                'usuario_id',
                'precio_venta',
				'cultivo_predefinido_id'
            ]);

            $cultivo->update($campos);

            DB::commit();
            return response()->json([
                "cultivo" => $cultivo,
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
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $cultivo = Cultivos::find($id);

            if($cultivo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Cultivo",
                ], 404);
            }

            $cultivo->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Cultivo eliminado correctamente"
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

    public function favorito(string $id, Request $request){
        DB::beginTransaction();
        try {
            $cultivo = Cultivos::find($id);

            if($cultivo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Cultivo",
                ], 404);
            }

            $usuario = User::find(auth()->user()->id);

            $usuario->cultivos_favoritos()->sync([$cultivo->id], false);

            DB::commit();
            return response()->json([
                "mensaje" => "Cultivo agregado a Fovoritos"
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

    public function favoritos(Request $request){
        $cultivos = User::find(auth()->user()->id)->cultivos_favoritos()->paginate($request->paginacion ?? 10);

        return response()->json([
            "cultivos" => $cultivos
        ]);
    }

    public function destroyFavorito(string $id){
        DB::beginTransaction();
        try {
            $cultivo = Cultivos::find($id);

            if($cultivo == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Cultivo",
                ], 404);
            }

            $usuario = User::find(auth()->user()->id);

            $usuario->cultivos_favoritos()->detach($cultivo->id);

            DB::commit();
            return response()->json([
                "mensaje" => "Cultivo eliminado de Fovoritos"
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


    public function categorias(Request $request){
        $categorias = Categoria::when($request->busca, function($query) use ($request){
            $query->where('nombre', 'like', '%' . $request->busca . '%');
        })
        ->where('estado', 1)
        ->paginate(10);

        return response()->json([
            "categorias" => $categorias
        ]);
    }

    public function usuarios(Request $request){
        $usuarios = User::select('users.*', DB::raw("CONCAT(nombres, ' ', apellidos) AS nombre_completo"))
        ->when($request->busca, function ($query) use ($request) {
            $query->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%{$request->busca}%"]);
        })
        ->whereHas('roles', function ($query) {
            $query->where('name', 'Vendedor');
        })
        ->where('estado', 1)
        ->paginate(10);

        return response()->json([
            "usuarios" => $usuarios
        ]);
    }

    public function detalle(string $id)
    {
        $producto = Cultivos::with([
            'usuario',
            'categoria',
            'galeria',
            'trazabilidad',
			'cultivo_predefinido'
        ])->find($id);

        if($producto == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el Producto",
            ], 404);
        }

        return response()->json([
            "producto" => $producto
        ]);
    }

    public function distribucionPrecios(Request $request)
    {
        $cultivos = Cultivos::where('estado', 1)
        ->where('usuario_id', auth()->user()->id)
        ->whereNull('pedido_id')
        ->get();

        return response()->json([
            "cultivos" => $cultivos
        ]);
    }

	public function ia(Request $request){

		set_time_limit(84000);

		$cultivos = CultivosPredefinidos::select('id', 'nombre')->get();
		
		$cultivos_strings_names = $cultivos->map(function($item) {
			return $item['id'].':'.$item['nombre'];
		})->implode(',');

		$ciudad_nombres = Ciudad::join('departamentos', 'departamentos.id', '=', 'ciudades.departamento_id')
        ->join('paises', 'paises.id', '=', 'departamentos.pais_id')
        ->select('ciudades.*', DB::raw("CONCAT(ciudades.nombre, ', ', departamentos.nombre, ', ', paises.nombre) AS nombre_completo"))
		->find($request->ciudad_id)->nombre_completo ?? "";

		$res = app(DeepseekService::class)->buscarCultivos($cultivos_strings_names, "{$request->latitud}, {$request->longitud}", $ciudad_nombres);

		$json_string = $res["choices"][0]['message']['content'] ?? ""; 

		// Decodificar el JSON (asegurarse de que sea un array asociativo)
		$data = json_decode($json_string, true);

		if (json_last_error() === JSON_ERROR_NONE) {
			// echo "JSON válido:\n";
			// print_r($data); // Aquí ya es un array asociativo limpio
			if (!empty($data['cultivos'])) {
				$data['cultivos'] = array_map(function($item) {
					$cultivo = CultivosPredefinidos::find($item['id'] ?? null);
					return array_merge($item, ['cultivo' => $cultivo]);
				}, $data['cultivos']);
			}
		} else {
			$data = "Error al decodificar JSON: " . json_last_error_msg();
			// $data = $res;
		}

		return response()->json($data);
	}

	public function recomendaciones(Request $request){

		set_time_limit(84000);

		if(!$request->validate([
			'latitud' => 'required',
			'longitud' => 'required'
		])){
			return response()->json([
				"error" => "Parametros no validos",
				"mensaje" => "Parametros no validos",
			], 400);
		}

		$client = new Client();
		$response = $client->request('POST', 'http://ml_service:5000/predict', [
			'connect_timeout' => 120000,
			'timeout' => 120000,
			'body' => json_encode([
				'lat' => $request->latitud,
				'long' => $request->longitud
			]),
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);

		$responseBody = $response->getBody()->getContents();
	
		if ($response->getStatusCode() !== 200) {
			return response()->json([
				"error" => "Error en el servicio ML",
				"mensaje" => $responseBody
			], 500);
		}
	
		$recomendaciones = json_decode($responseBody, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			return response()->json([
				"error" => "Error al decodificar la respuesta del servicio ML",
				"mensaje" => json_last_error_msg()
			], 500);
		}

		$datos_climaticos = $recomendaciones['datos_climaticos'];
		$recomendaciones = $recomendaciones['recomendaciones'];

		//debemos ahora aplicar el peso de los factores para recomendar uno u otro, 
		$factores_aplican = Factores::where('ciudad_id', $request->ciudad_id)
		->where('fecha_inicio', '<=', Carbon::now())
		->where('fecha_fin', '>=', Carbon::now())
		->when($request->latitud && $request->longitud, function ($query) use ($request) {
			$latitud = $request->latitud;
			$longitud = $request->longitud;
		
			$query->whereRaw("(
				6371 * acos(
					cos(radians(CAST(? AS double precision)))
					* cos(radians(CAST(latitud AS double precision)))
					* cos(radians(CAST(longitud AS double precision)) - radians(CAST(? AS double precision)))
					+ sin(radians(CAST(? AS double precision)))
					* sin(radians(CAST(latitud AS double precision)))
				)
			) <= CAST(radio AS double precision)", [$latitud, $longitud, $latitud]);
		})
		->with(['cultivos_predefinidos_factores'])
		->select('id', 'nombre', 'descripcion')
		->get();

		$cultivos_predefinidos = CultivosPredefinidos::whereIn('nombre', array_map(function($cultivo) {
			return $cultivo[0];
		}, $recomendaciones))
		->select('id', 'nombre', 'imagen', 'nombre_corto')
		->get();


		//ahora buscamos con ia
		$cultivos_strings_names = $cultivos_predefinidos->map(function($item) {
			return $item['id'].':'.$item['nombre'];
		})->implode(',');

		logger($factores_aplican);

		$ciudad_nombres = Ciudad::join('departamentos', 'departamentos.id', '=', 'ciudades.departamento_id')
		->join('paises', 'paises.id', '=', 'departamentos.pais_id')
		->select('ciudades.*', DB::raw("CONCAT(ciudades.nombre, ', ', departamentos.nombre, ', ', paises.nombre) AS nombre_completo"))
		->find($request->ciudad_id)->nombre_completo ?? "";

		$res = app(DeepseekService::class)->complementarCultivos($cultivos_strings_names, "{$request->latitud}, {$request->longitud}", $ciudad_nombres);

		$cultivos_ai = $this->convertJsonIA($res)['cultivos'] ?? [];

		$cultivos_predefinidos->each(function($cultivo_predefinido) use ($factores_aplican, $recomendaciones, $cultivos_ai) {
			// $cultivo_predefinido->recomendacion = $recomendaciones[$cultivo_predefinido->nombre];
			$cultivo_predefinido->peso = $factores_aplican->filter(function ($factor) use ($cultivo_predefinido) {
				return $factor->cultivos_predefinidos_factores->contains('cultivo_predefinido_id', $cultivo_predefinido->id);
			})->avg('peso') ?? 0;
			
			$cultivo_predefinido->factores = $factores_aplican->filter(function ($factor) use ($cultivo_predefinido) {
				return $factor->cultivos_predefinidos_factores->contains('cultivo_predefinido_id', $cultivo_predefinido->id);
			})->pluck('descripcion', 'nombre')->values()->toArray();

			$match = array_values(array_filter($recomendaciones, function ($cultivo) use ($cultivo_predefinido) {
				return $cultivo[0] === $cultivo_predefinido->nombre;
			}));
			
			$cultivo_predefinido->peso_modelo = !empty($match) ? $match[0][1] : 0;

			$cultivo_predefinido->peso_total = $cultivo_predefinido->peso + $cultivo_predefinido->peso_modelo;

			$match_razones = array_values(array_filter($cultivos_ai, function ($cultivo) use ($cultivo_predefinido) {
				return $cultivo['id'] == $cultivo_predefinido->id;
			}));

			$cultivo_predefinido->razones = !empty($match_razones) ? $match_razones[0]['razones'] : [];
		});

		logger($cultivos_predefinidos->sortByDesc('peso_total')->values()->toArray());
	
		return response()->json([
			"message" => "Prediccion hecha",
			"recomendaciones" => $cultivos_predefinidos->sortByDesc('peso_total')->values()->toArray(),
			"datos_climaticos" => $datos_climaticos,
		]);	
	}

	public function convertJsonIA($res){
		$json_string = $res["choices"][0]['message']['content'] ?? ""; 

		// Decodificar el JSON (asegurarse de que sea un array asociativo)
		$data = json_decode($json_string, true);

		if (json_last_error() === JSON_ERROR_NONE) {
			// echo "JSON válido:\n";
			// print_r($data); // Aquí ya es un array asociativo limpio
			if (!empty($data['cultivos'])) {
				$data['cultivos'] = array_map(function($item) {
					$cultivo = CultivosPredefinidos::find($item['id'] ?? null);
					return array_merge($item, ['cultivo' => $cultivo]);
				}, $data['cultivos']);
			}
		} else {
			$data = "Error al decodificar JSON: " . json_last_error_msg();
			// $data = $res;
		}

		return $data;
	}
}
