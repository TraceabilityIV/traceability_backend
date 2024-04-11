<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Precios\ActualizarRequest;
use App\Http\Requests\Precios\CrearRequest;
use App\Models\Precio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PreciosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $precios = Precio::when($request->cultivo_id, function ($query) use ($request) {
            $query->where('cultivo_id', $request->cultivo_id);
        })
        ->when($request->tipo_id, function ($query) use ($request) {
            $query->where('tipo_id', $request->tipo_id);
        })
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "precios" => $precios
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
            $campos = $request->only('precio_venta', 'cultivo_id', 'tipo_id', 'estado');

            $precio = Precio::create($campos);

            DB::commit();
            return response()->json([
                "precio" => $precio,
                "mensaje" => "Precio creado correctamente"
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
            $precio = Precio::find($id);

            if($precio == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Precio",
                ], 404);
            }

            $campos = $request->only('precio_venta', 'tipo_id', 'estado');

            $precio->update($campos);

            DB::commit();
            return response()->json([
                "precio" => $precio,
                "mensaje" => "Precio actualizado correctamente"
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
            $precio = Precio::find($id);

            if($precio == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Precio",
                ], 404);
            }

            $precio->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Precio eliminado correctamente"
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
