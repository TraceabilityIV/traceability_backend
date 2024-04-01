<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permisos\CrearRequest;
use App\Models\Permisos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermisosController extends Controller
{

    public function __construct()
    {
        // $this->middleware('permission:Ver Permisos')->only('index');
        // $this->middleware('permission:Editar Permisos')->only('store');
        // $this->middleware('permission:Crear Permisos')->only('update');
        // $this->middleware('permission:Eliminar Permisos')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $permisos = Permisos::where('guard_name', 'api')->paginate($request->paginacion ?? 10);

        return response()->json([
            "permisos" => $permisos
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
        $permiso = Permisos::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return response()->json([
            "permiso" => $permiso,
            "mensaje" => "Permiso creado correctamente"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $permiso = Permisos::find($id);

        if($permiso == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el permiso",
            ], 404);
        }

        return response()->json([
            "permiso" => $permiso,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permiso = Permisos::find($id);

        if($permiso == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el permiso",
            ], 404);
        }

        return response()->json([
            "permiso" => $permiso,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CrearRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $permiso = Permisos::find($id);

            if($permiso == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el permiso",
                ], 404);
            }

            $permiso->update([
                'name' => $request->name
            ]);

            DB::commit();
            return response()->json([
                "permiso" => $permiso,
                "mensaje" => "Permiso actualizado correctamente"
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
            $permiso = Permisos::find($id);

            if($permiso == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el permiso",
                ], 404);
            }

            $permiso->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Permiso eliminado correctamente"
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
