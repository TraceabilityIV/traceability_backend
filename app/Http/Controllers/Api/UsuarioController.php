<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuarios\RegistrarRequest;
use App\Http\Requests\Usuarios\TokenRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function token(TokenRequest $request){

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Credenciales Invalidas',
                'mensaje' => 'Correo o Contraseña Incorrecta'
            ], 400);
        }

        return response()->json([
            'mensaje' => 'Token obtenido',
            'token' => $user->createToken($request->device_name)->plainTextToken
        ]);
    }

    public function resgistrar(RegistrarRequest $request){

        $usuario = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos ?? '',
            'telefono' =>  $request->telefono,
            'estado' => $request->estado ?? 1,
            'avatar' => $request->avatar ?? null,
            'doc_identificacion' => $request->doc_identificacion ?? null,
            'rut' => $request->rut ?? null,
            'contrato' => $request->contrato ?? null,
        ]);

        return response()->json([
            'mensaje' => 'Usuario creado correctamente',
            'usuario' => $usuario
        ]);
    }
}
