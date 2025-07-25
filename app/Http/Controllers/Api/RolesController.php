<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\CrearRequest;
use App\Http\Requests\Roles\RolesPermisosRequest;
use App\Models\Permisos;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Roles::where('guard_name', 'api')
		->when($request->filled('buscar'), function($query) use ($request){
			$query->where('name', 'like', '%' . $request->buscar . '%');
		})
		->paginate($request->paginacion ?? 10);

        return response()->json([
            "roles" => $roles
        ]);
    }

    public function all(Request $request)
    {
        $roles = Roles::where('guard_name', 'api')->get();

        return response()->json([
            "roles" => $roles
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
        $rol = Roles::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return response()->json([
            "rol" => $rol,
            "mensaje" => "Rol creado correctamente"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rol = Roles::find($id);

        if($rol == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el eol",
            ], 404);
        }

        return response()->json([
            "rol" => $rol,
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
    public function update(CrearRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $rol = Roles::find($id);

            if($rol == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el rol",
                ], 404);
            }

            $rol->update([
                'name' => $request->name
            ]);

            DB::commit();
            return response()->json([
                "rol" => $rol,
                "mensaje" => "Rol actualizado correctamente"
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
            $rol = Roles::find($id);

            if($rol == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el rol",
                ], 404);
            }

            $rol->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Rol eliminado correctamente"
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

    public function permisos(string $id)
    {
        $rol = Roles::find($id);

        if($rol == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el rol",
            ], 404);
        }

        return response()->json([
            "permisos" => $rol->permissions
        ]);
    }

    public function permisoRol(string $rol, string $permiso){
        $rol = Roles::find($rol);

        if($rol == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el rol",
            ], 404);
        }

        $permiso = Permisos::find($permiso);

        if($rol == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el permiso",
            ], 404);
        }

        $permiso->assignRole($rol);

        return response()->json([
            "permiso" => $permiso,
            "mensaje" => "Permiso asignado correctamente"
        ]);
    }

    public function sincronizarPermisos(RolesPermisosRequest $request, string $id){
        $rol = Roles::find($id);

        if($rol == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el rol",
            ], 404);
        }

        // $permisos = Permisos::whereIn('id', $request->permisos)->pluck('name')->toArray();

        $rol->syncPermissions($request->permisos);

        return response()->json([
            "permisos" => $rol->permissions,
            "mensaje" => "Permisos sincronizados correctamente"
        ]);
    }

    public function agregarPermisos(RolesPermisosRequest $request, string $id){
        $rol = Roles::find($id);

        if($rol == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el rol",
            ], 404);
        }

        $rol->givePermissionTo($request->permisos);

        return response()->json([
            "mensaje" => "Permisos agregados correctamente"
        ]);
    }
}
