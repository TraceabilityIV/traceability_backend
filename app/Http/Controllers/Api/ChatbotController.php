<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatBot\ActualizarRequest;
use App\Http\Requests\ChatBot\CrearRequest;
use App\Models\ChatBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $chat_bot = ChatBot::paginate($request->paginacion ?? 10);

        return response()->json([
            "chat_bot" => $chat_bot
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
            $campos = $request->only('mensaje', 'descripcion', 'accion');

            $chatbot = ChatBot::create($campos);

            DB::commit();
            return response()->json([
                "chatbot" => $chatbot,
                "mensaje" => "Mensaje de Chat Bot creado correctamente"
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActualizarRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $chatbot = ChatBot::find($id);

            if($chatbot == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el mensaje",
                ], 404);
            }

            $campos = $request->only('mensaje', 'descripcion', 'accion');

            $chatbot->update($campos);

            DB::commit();
            return response()->json([
                "chatbot" => $chatbot,
                "mensaje" => "Mensaje de ChatBot actualizado correctamente"
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
            $chatbot = ChatBot::find($id);

            if($chatbot == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el mensaje de ChatBot",
                ], 404);
            }

            $chatbot->delete();

            DB::commit();
            return response()->json([
                "mensaje" => "Mensaje de ChatBot eliminado correctamente"
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
