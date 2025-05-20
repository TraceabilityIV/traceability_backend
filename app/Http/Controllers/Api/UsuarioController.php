<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuarios\ActualizarRequest;
use App\Http\Requests\Usuarios\AsignarRolRequest;
use App\Http\Requests\Usuarios\RegistrarRequest;
use App\Http\Requests\Usuarios\TokenRequest;
use App\Models\Roles;
use App\Models\User;
use Carbon\Carbon;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function __construct(){
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
        $usuarios = User::when($request->filled('buscar'), function($query) use ($request){
            $query->where('nombres', 'like', '%' . $request->buscar . '%')
				->orWhere('apellidos', 'like', '%' . $request->buscar . '%')
                ->orWhere('email', 'like', '%' . $request->buscar . '%')
                ->orWhere('telefono', 'like', '%' . $request->buscar . '%');
        })->paginate($request->paginacion ?? 10);

        return response()->json([
            "usuarios" => $usuarios
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
    public function store(RegistrarRequest $request)
    {
        $campos = $request->only('email', 'password', 'nombres', 'apellidos', 'telefono', 'estado', 'avatar', 'doc_identificacion', 'rut', 'contrato');

        //cargamos las imagenes en tal caso
        if($request->hasFile('avatar')){
            $campos['avatar'] = $request->file('avatar')->hashName();
            $request->file('avatar')->storeAs('public/usuarios/avatars', $campos['avatar']);
            $campos['avatar'] = url('storage/usuarios/avatars/' . $campos['avatar']);
        }
        if($request->hasFile('doc_identificacion')){
            $campos['doc_identificacion'] = $request->file('doc_identificacion')->hashName();
            $request->file('doc_identificacion')->storeAs('public/usuarios/doc_identificacion', $campos['doc_identificacion']);
            //obtenemos la ruta de una vez
            $campos['doc_identificacion'] = url('storage/usuarios/doc_identificacion/' . $campos['doc_identificacion']);
        }
        if($request->hasFile('rut')){
            $campos['rut'] = $request->file('rut')->hashName();
            $request->file('rut')->storeAs('public/usuarios/ruts', $campos['rut']);
            $campos['rut'] = url('storage/usuarios/ruts/' . $campos['rut']);
        }
        if($request->hasFile('contrato')){
            $campos['contrato'] = $request->file('contrato')->hashName();

            $request->file('contrato')->storeAs('public/usuarios/contratos', $campos['contrato']);

            $campos['contrato'] = url('storage/usuarios/contratos/' . $campos['contrato']);
        }

        if(isset($campos['doc_identificacion']) && isset($campos['rut']) && isset($campos['contrato'])){
            $campos['paso_validacion_documentos'] = 'Subidos';
        }

        $usuario = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password ?? ""),
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos ?? '',
            'telefono' => $request->telefono,
            'estado' => $request->estado ?? true,
            'avatar' => $campos['avatar'] ?? null,
            'doc_identificacion' => $request->doc_identificacion ?? null,
            'rut' => $campos['rut'] ?? null,
            'contrato' => $campos['contrato'] ?? null,
        ]);

        $usuario->guard(['api'])->assignRole(Role::whereIn('id', $request->get('roles', []))->get());

        // try {
        //     $usuario->assignRole("Cliente");
        // } catch (\Throwable $th) {
        //     Log::error("El Rol de Cliente no existe");
        // }

        return response()->json([
            "usuario" => $usuario,
            "mensaje" => "Usuario creado correctamente"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $usuario = User::with([
            'roles'
        ])->find($id);

        if($usuario == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el usuario",
            ], 404);
        }

        return response()->json([
            "usuario" => $usuario,
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
            $usuario = User::find($id);

            if($usuario == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el usuario",
                ], 404);
            }

            if(isset($request->roles))
                $usuario->guard(['api'])->syncRoles(Role::whereIn('id', $request->get('roles', []))->get());

            $campos = $request->only('email', 'password', 'nombres', 'apellidos', 'telefono', 'estado', 'avatar', 'doc_identificacion', 'rut', 'contrato');

            if($request->filled('password')){
                $campos['password'] = Hash::make($request->password);
            }

            if($request->hasFile('avatar')){
                $campos['avatar'] = $request->file('avatar')->hashName();
                $request->file('avatar')->storeAs('public/usuarios/avatars', $campos['avatar']);
                $campos['avatar'] = url('storage/usuarios/avatars/' . $campos['avatar']);
            }
            if($request->hasFile('doc_identificacion')){
                $campos['doc_identificacion'] = $request->file('doc_identificacion')->hashName();
                $request->file('doc_identificacion')->storeAs('public/usuarios/doc_identificacion', $campos['doc_identificacion']);
                //obtenemos la ruta de una vez
                $campos['doc_identificacion'] = url('storage/usuarios/doc_identificacion/' . $campos['doc_identificacion']);
            }
            if($request->hasFile('rut')){
                $campos['rut'] = $request->file('rut')->hashName();
                $request->file('rut')->storeAs('public/usuarios/ruts', $campos['rut']);
                $campos['rut'] = url('storage/usuarios/ruts/' . $campos['rut']);
            }
            if($request->hasFile('contrato')){
                $campos['contrato'] = $request->file('contrato')->hashName();

                $request->file('contrato')->storeAs('public/usuarios/contratos', $campos['contrato']);

                $campos['contrato'] = url('storage/usuarios/contratos/' . $campos['contrato']);
            }

            if(isset($campos['doc_identificacion']) && isset($campos['rut']) && isset($campos['contrato'])){
                $campos['paso_validacion_documentos'] = 'Subidos';
            }

            $usuario->update($campos);

            DB::commit();
            return response()->json([
                "usuario" => $usuario,
                "mensaje" => "Usuario actualizado correctamente"
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
            $usuario = User::find($id);

            if($usuario == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el usuario",
                ], 404);
            }

            $usuario->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Usuario eliminado correctamente"
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

    public function token(TokenRequest $request){

        $user = User::withCount('roles')->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Credenciales Invalidas',
                'mensaje' => 'Correo o Contraseña Incorrecta'
            ], 400);
        }

        if($user->roles_count == 1){
            $user->load('roles');
        }

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'social' => 'login',
            'user' => $user
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
            'tipo_cliente' => $request->tipo_cliente ?? 'Cliente',
            'documentacion_valida' => null,
            'paso_validacion_documentos' => isset($request->tipo_cliente) && $request->tipo_cliente == 'Vendedor' ? 'Pedientes' : null,
        ]);
        logger($usuario);
        try {
            $usuario->guard(['api'])->assignRole("Cliente");
        } catch (\Throwable $th) {
            Log::error("El Rol de Cliente no existe");
        }

        // try {
        //     if(isset($request->tipo_cliente) && $request->tipo_cliente == 'Vendedor'){
        //         $usuario->assignRole("Vendedor");
        //     }
        // } catch (\Throwable $th) {
        //     Log::error("El Rol de Vendedor no existe");
        // }
        // logger($usuario);
        return response()->json([
            'mensaje' => 'Usuario creado correctamente',
            'usuario' => $usuario,
            'social' => 'login',
            'token' => $usuario->createToken($request->device_name)->plainTextToken
        ]);
    }

    public function roles(){
        $roles = User::with('roles')->find(auth()->id());

        return response()->json([
            'roles' => $roles->roles ?? []
        ]);
    }

    public function logout(Request $request){

        auth()->user()->tokens()->delete();

        return response()->json([
            'mensaje' => 'Sesion Cerrada',
        ]);
    }

    public function subirArchivos(Request $request){

    }

    public function google(Request $request){
        $client = new Google_Client(['client_id' => '639809216045-i30bfvtlg1lunog0jj0u6ib1u2be4q3g.apps.googleusercontent.com']);
        $payload = $client->verifyIdToken($request->tokenId);

        if ($payload) {
            $usuario = User::where('email', $payload['email'])->first();


            if($usuario == null){
                $usuario = User::create([
                    'email' => $payload['email'],
                    'password' => "",
                    'nombres' => $payload['given_name'],
                    'apellidos' => $payload['family_name'] ?? '',
                    'telefono' =>  0,
                    'estado' => 1,
                    'avatar' => $payload['picture'] ?? null,
                    'email_verified_at' => Carbon::now()
                ]);

                try {
                    $usuario->assignRole("Cliente");
                } catch (\Throwable $th) {
                    Log::error("El Rol de Cliente no existe");
                }
            }

            $token = $usuario->createToken($request->device_name)->plainTextToken;

            return response()->json([
                'token' => $token,
                'social' => 'google',
                'user' => $usuario
            ]);
        } else {
          return response()->json([
            'error' => 'Error del token',
            'mensaje' => "Token Invalido"
          ], 500);
        }
    }

    public function asignarRol(AsignarRolRequest $request){
        $user = User::find($request->usuario_id);

        if($user == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el usuario",
            ], 404);
        }

        $roles = $request->rol_id ?? $request->roles_id;

        if(!is_array($roles)){
            $roles = [$roles];
        }

        $user->guard(['api'])->assignRole(Role::whereIn('id', $roles)->get());

        return response()->json([
            "mensaje" => "Rol(es) asignado(s) correctamente"
        ]);
    }

    public function validation(Request $request){
        return response()->json([
            'usuario_validado' => auth()->check()
        ]);
    }

    public function usuarioActual(){

        $usuario = User::find(auth()->id());

        if($usuario == null){
            return response()->json([
                "error" => "No encontrado",
                "mensaje" => "No se encontro el usuario",
            ], 404);
        }

        return response()->json([
            'usuario' => $usuario
        ]);
    }
}
