<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\CrearRequest;
use App\Http\Requests\Menu\EditarRequest;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:Ver Menus')->only('index');
        // $this->middleware('permission:Editar Menus')->only('store');
        // $this->middleware('permission:Crear Menus')->only('update');
        // $this->middleware('permission:Eliminar Menus')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $menus = Menu::paginate($request->paginacion ?? 10);

        return response()->json([
            "menus" => $menus
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

        $menu = Menu::create($request->only('nombre', 'id_referencia', 'icono', 'color', 'tipo_icono', 'estado', 'permiso_id'));

        return response()->json([
            "menu" => $menu,
            "mensaje" => "Menu creado correctamente"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $menu = Menu::find($id);

        if($menu == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el menu",
            ], 404);
        }

        return response()->json([
            "menu" => $menu,
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
    public function update(EditarRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $menu = Menu::find($id);

            if($menu == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el menu",
                ], 404);
            }

            $menu->update($request->only('nombre', 'id_referencia', 'icono', 'color', 'tipo_icono', 'estado', 'permiso_id'));

            DB::commit();
            return response()->json([
                "menu" => $menu,
                "mensaje" => "Menu actualizado correctamente"
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
            $menu = Menu::find($id);

            if($menu == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el menu",
                ], 404);
            }

            $menu->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Menu eliminado correctamente"
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
