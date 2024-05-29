<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categorias\ActualizarRequest;
use App\Http\Requests\Categorias\CrearRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoriasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->todas){
            $categorias = Categoria::get();
        }else{
            $categorias = Categoria::paginate($request->paginacion ?? 10);
        }

        return response()->json([
            "categorias" => $categorias
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
            $campos = $request->only('nombre', 'nombre_corto', 'estado');

            if($request->hasFile('imagen')){
                $campos['imagen'] = $request->file('imagen')->hashName();
                $request->file('imagen')->storeAs('public/categorias/', $campos['imagen']);
                $campos['imagen'] = url('storage/categorias/' . $campos['imagen']);
            }

            $categoria = Categoria::create($campos);

            DB::commit();
            return response()->json([
                "categoria" => $categoria,
                "mensaje" => "Categoria creada correctamente"
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
        $categoria = Categoria::find($id);

        if($categoria == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro la Categoría",
            ], 404);
        }

        return response()->json([
            "categoria" => $categoria
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
            $categoria = Categoria::find($id);

            if($categoria == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Categoria",
                ], 404);
            }

            $campos = $request->only('nombre', 'nombre_corto', 'estado');

            if($request->hasFile('imagen')){
                $campos['imagen'] = $request->file('imagen')->hashName();
                $request->file('imagen')->storeAs('public/categorias/', $campos['imagen']);
                $campos['imagen'] = url('storage/categorias/' . $campos['imagen']);
            }

            $categoria->update($campos);

            DB::commit();
            return response()->json([
                "categoria" => $categoria,
                "mensaje" => "Categoria actualizada correctamente"
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
            $categoria = Categoria::find($id);

            if($categoria == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro la Categoria",
                ], 404);
            }

            $categoria->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Categoria eliminado correctamente"
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

    public function masVendidas(Request $request){
        $categorias = Categoria::where('estado', 1)
        ->whereHas('cultivos', function ($query) {
            $query->whereNotNull('pedido_id')->whereNotNull('cantidad_aproximada');
        })
        ->withSum(['cultivos' => function ($query) {
            $query->whereNotNull('pedido_id')->whereNotNull('cantidad_aproximada');
        }], 'cantidad_aproximada')
        ->orderBy('cultivos_sum_cantidad_aproximada', 'DESC')
        ->paginate(5);

        return response()->json([
            "categorias" => $categorias
        ]);
    }
}
