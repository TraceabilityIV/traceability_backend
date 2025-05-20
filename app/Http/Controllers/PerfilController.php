<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Usuarios\ActualizarRequest;
use App\Http\Requests\Usuarios\PerfilRequest;
use App\Models\User;


class PerfilController extends Controller
{
	public function index(): JsonResponse
	{
        $usuario = User::with([
			'roles',
		])->find(auth()->id());
		
        if ($usuario == null) {
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontró el usuario",
            ], 404);
        }

        return response()->json([
            "usuario" => $usuario
        ], 200);
	}
	
    public function show(): JsonResponse
    {
        $usuario = Auth::user();
        if ($usuario == null) {
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontró el usuario",
            ], 404);
        }
        return response()->json([
            "usuario" => $usuario
        ], 200);
    }
	public function store(PerfilRequest $request): JsonResponse
    {
        $usuario = User::find(auth()->id());

        if ($usuario == null) {
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontró el usuario",
            ], 404);
        }

        try {
            $campos = $request->only('email', 'password', 'nombres', 'apellidos', 'telefono', 'estado', 'avatar', 'tipo_cliente');

            if ($request->filled('password')) {
                $campos['password'] = bcrypt($request->password);
            }

            if ($request->hasFile('avatar')) {
                $campos['avatar'] = $request->file('avatar')->hashName();
                $request->file('avatar')->storeAs('public/usuarios/avatars', $campos['avatar']);
                $campos['avatar'] = url('storage/usuarios/avatars/' . $campos['avatar']);
            }

            $usuario->update($campos);

            return response()->json([
                "usuario" => $usuario,
                "mensaje" => "Perfil actualizado correctamente"
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th);

            return response()->json([
                "error" => "Error del servidor",
                "mensaje" => $th->getMessage(),
            ], 500);
        }

    }
    public function update(ActualizarRequest $request): JsonResponse
    {
        $usuario = User::find(auth()->id());

        if ($usuario == null) {
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontró el usuario",
            ], 404);
        }

        try {
            $campos = $request->only('email', 'password', 'nombres', 'apellidos', 'telefono', 'estado', 'avatar', 'tipo_cliente');

            if ($request->filled('password')) {
                $campos['password'] = bcrypt($request->password);
            }

            if ($request->hasFile('avatar')) {
                $campos['avatar'] = $request->file('avatar')->hashName();
                $request->file('avatar')->storeAs('public/usuarios/avatars', $campos['avatar']);
                $campos['avatar'] = url('storage/usuarios/avatars/' . $campos['avatar']);
            }

            $usuario->update($campos);

            return response()->json([
                "usuario" => $usuario,
                "mensaje" => "Perfil actualizado correctamente"
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th);

            return response()->json([
                "error" => "Error del servidor",
                "mensaje" => $th->getMessage(),
            ], 500);
        }

    }
}
