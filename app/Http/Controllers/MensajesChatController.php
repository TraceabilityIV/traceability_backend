<?php

namespace App\Http\Controllers;

use App\Http\Requests\MensajesChat\CrearRequest;
use App\Http\Requests\MensajesChat\IndexRequest;
use App\Models\MensajesChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MensajesChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $mensajes = MensajesChat::where(function($query) use ($request) {
            $query->where('usuario_envia_id', $request->usuario_envia_id)
            ->where('usuario_recibe_id', $request->usuario_recibe_id);
        })->orWhere(function($query) use ($request) {
            $query->where('usuario_envia_id', $request->usuario_recibe_id)
            ->where('usuario_recibe_id', $request->usuario_envia_id);
        })
        ->orderBy('created_at', 'ASC')
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "mensajes" => $mensajes
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
            $campos = $request->only('mensaje', 'usuario_envia_id', 'usuario_recibe_id', 'tipo');

            if($campos['tipo'] != 'texto'){
                if($request->hasFile('mensaje')){
                    $campos_mensaje = [];
                    $campos['nombre'] = $request->file('mensaje')->hashName();
                    $request->file('mensaje')->storeAs('public/mensajes_chat', $campos['nombre']);
                    $campos['url'] = url('storage/mensajes_chat/' . $campos['nombre']);
                    $campos['nombre'] = $request->file('galeria')->getClientOriginalName();

                    $campos['mensaje'] = json_encode($campos_mensaje);
                }else{
                    return response()->json([
                        "error" => "Error del servidor",
                        "mensaje" => "Debe subir un archivo"
                    ]);
                }
            }

            $mensaje = MensajesChat::create($campos);

            DB::commit();
            return response()->json([
                "mensaje_chat" => $mensaje,
                "mensaje" => "Mensaje creado correctamente"
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
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $mensaje = MensajesChat::find($id);

            if($mensaje == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Mensaje",
                ], 404);
            }

            $campos = $request->only('mensaje', 'tipo');

            if(isset($campos['tipo']) && $campos['tipo'] != 'texto'){
                if($request->hasFile('mensaje')){
                    $campos_mensaje = [];
                    $campos['nombre'] = $request->file('mensaje')->hashName();
                    $request->file('mensaje')->storeAs('public/mensajes_chat', $campos['nombre']);
                    $campos['url'] = url('storage/mensajes_chat/' . $campos['nombre']);
                    $campos['nombre'] = $request->file('galeria')->getClientOriginalName();

                    $campos['mensaje'] = json_encode($campos_mensaje);
                }
            }

            $mensaje->update($campos);

            DB::commit();
            return response()->json([
                "mensaje_chat" => $mensaje,
                "mensaje" => "Mensaje actualizado correctamente"
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
            $mensaje = MensajesChat::find($id);

            if($mensaje == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el mensaje",
                ], 404);
            }

            $mensaje->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Mensaje eliminado correctamente"
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
