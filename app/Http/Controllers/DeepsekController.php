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
		set_time_limit(0);

		$query = $request->input('query');
		$options = $request->input('options', []);

		// Crear el arreglo de mensajes necesario para la API
		// $messages = [
		// 	["role" => "system", "content" => "Habla en español y debes de responder solo con los nombres y las razones de porque cultivarlo en el punto indicado, en formato JSON válido sin caracteres de escape ni texto adicional."],
		// 	["role" => "system", "content" => "Predices que puedes cultivar en puntos especificos del planeta Tierra, teniendo en cuenta la ubicacion (especifica), la temperatura, el clima y la humedad."],
		// 	["role" => "system", "content" => "Los cultivos que tenemos en el sistema son, Granadilla, Papa, Lechuga, Maiz, Arroz."],
		// 	["role" => "user", "content" => "Que debo cultivar en la siguiente ubicación 2.984822, -75.271907"]
		// ];

		$messages = [
			[
				"role" => "system",
				"content" => "Habla en español y responde solo con los nombres de los cultivos y las razones para cultivarlos en el punto indicado. 
							  Solo devuelve el objeto JSON con los nombres de los cultivos y sus razones. 
							  No incluyas ```json ni ``` al inicio o final de la respuesta.
							  Eres un esperto qué sabe que se puede cultivar en puntos específicos del planeta Tierra, teniendo en cuenta la ubicación (latitud y longitud), 
							  la temperatura, el clima y la humedad. Además debes tener en cuenta mas factores, como plagas, enefermedades, ayudas gubernamentales, etc. Todo esto obteniendolo de notificias locales de la ubicación.
							  Los resultados debes ser ordenados teniendo en cuenta la rentabilidad, la demanda y oferta al rededor de la ubicación indicada.
							  Los cultivos disponibles en el sistema son: Granadilla, Papa, Lechuga, Maíz y Arroz."
			],
			[
				"role" => "user",
				"content" => "¿Qué debo cultivar en la siguiente ubicación 2.984822, -75.271907, queda alrededor de Neiva, Huila, Colombia?"
			]
		];

		// Pasar los mensajes a tu método HolaMundo
		$results = $this->deepsekService->HolaMundo($messages, 'deepseek-reasoner', $options);

		$json_string = $results["choices"][0]['message']['content'] ?? ""; 

		// Decodificar el JSON (asegurarse de que sea un array asociativo)
		$data = json_decode($json_string, true);

		if (json_last_error() === JSON_ERROR_NONE) {
			// echo "JSON válido:\n";
			// print_r($data); // Aquí ya es un array asociativo limpio
		} else {
			$data = "Error al decodificar JSON: " . json_last_error_msg();
		}

		return response()->json($data);
	}
}
