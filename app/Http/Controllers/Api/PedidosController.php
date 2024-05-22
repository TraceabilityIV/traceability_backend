<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pedidos\ActualizarRequest;
use App\Http\Requests\Pedidos\CrearRequest;
use App\Models\Cultivos;
use App\Models\Estado;
use App\Models\HistorialEstadosPedido;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pedidos = Pedido::where('usuario_id', auth()->id())
        ->with('cultivo.imagen', 'usuario')
        ->paginate($request->paginacion ?? 10);

        return response()->json([
            "pedidos" => $pedidos
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
            $estado = Estado::where('flag_inicial', 1)->first();

            if($estado == null){
                return response()->json([
                    "error" => "Error de estado",
                    "mensaje" => "El estado inicial no existe"
                ], 400);
            }

            $campos = $request->only('total', 'subtotal', 'saldo', 'metodo_pago', 'tipo_pago');

            $campos['usuario_id'] = auth()->user()->id;
            $campos['estado_pedido_id'] = $estado->id ?? null;

            $pedido = Pedido::create($campos);

            $cultivo = Cultivos::find($request->cultivo_id);

            if($cultivo->pedido_id != null){
                DB::rollBack();
                return response()->json([
                    "error" => "Error de cultivo",
                    "mensaje" => "El cultivo ya esta vendido"
                ], 400);
            }

            $cultivo->update([
                'pedido_id' => $pedido->id
            ]);

            DB::commit();
            return response()->json([
                "pedido" => $pedido,
                "mensaje" => "Pedido generado correctamente"
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
            $pedido = Pedido::find($id);

            if($pedido == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Pedido",
                ], 404);
            }

            $campos = $request->only('direccion_id', 'metodo_pago', 'tipo_pago');

            $pedido->update($campos);


            DB::commit();
            return response()->json([
                "pedido" => $pedido,
                "mensaje" => "Pedido actualizado correctamente"
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
        //
    }

    public function avanzar(string $id)
    {
        DB::beginTransaction();
        try {
            $pedido = Pedido::with(['estado'])->find($id);

            if($pedido == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No se encontro el Pedido",
                ], 404);
            }

            if($pedido->estado->flag_final == 1){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "El pedido ya se encuentra finalizado",
                ], 404);
            }else if($pedido->estado->estado_siguiente_id == null){
                return response()->json([
                    "error" => "No encontrado",
                    "mensaje" => "No hay mas estados de avanzar",
                ], 404);
            }

            HistorialEstadosPedido::create([
                'pedido_id' => $pedido->id,
                'estado_id' => $pedido->estado->id,
                'usuario_id' => auth()->user()->id,
                'estado_siguiente_id' => $pedido->estado->estado_siguiente_id,
            ]);


            $pedido->update([
                'estado_pedido_id' => $pedido->estado->estado_siguiente_id
            ]);


            DB::commit();
            return response()->json([
                "pedido" => $pedido,
                "mensaje" => "Pedido actualizado correctamente"
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
