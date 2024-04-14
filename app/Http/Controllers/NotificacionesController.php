<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use App\Models\User;
use App\Notifications\CompraNotificacion;
use Illuminate\Http\Request;

class NotificacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $notificaciones  = auth()->user()->notifications();
        // $notificaciones = Notifications::get();
        $user = User::find( auth()->user()->id);

        return response()->json([
            "notificaciones" => $user->notifications
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
    public function store(Request $request)
    {
        auth()->user()->notify(new CompraNotificacion(56));

        return response()->json([
            "message" => "Notificación enviada"
        ]);
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
}
