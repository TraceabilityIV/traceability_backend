<?php

namespace App\Http\Controllers;

use App\Services\DeepseekService;
use Illuminate\Http\Request;

class DeepsekController extends Controller
{
    protected $deepsekService;

    public function __construct(DeepseekService $deepsekService)
    {
        $this->deepsekService = $deepsekService;
    }

    public function search(Request $request)
{
    $query = $request->input('query');
    $options = $request->input('options', []);

    // Crear el arreglo de mensajes necesario para la API
    $messages = [
        ["role" => "system", "content" => "You are a helpful assistant."],
        ["role" => "user", "content" => $query]
    ];

    // Pasar los mensajes a tu método HolaMundo
    $results = $this->deepsekService->HolaMundo($messages, 'deepseek-chat', $options);

    return response()->json($results);
}
}
