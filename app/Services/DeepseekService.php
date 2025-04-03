<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class DeepseekService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('DEEPSEEK_API_KEY');
        $this->baseUrl = 'https://api.deepseek.com';
        $this->client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function HolaMundo($messages, $model = 'deepseek-chat', $options = [])
    {
        try {
            $response = $this->client->post($this->baseUrl . '/chat/completions', [
                'json' => array_merge([
                    'model' => $model,
                    'messages' => $messages,
                    'stream' => false
                ], $options)
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

	public function buscarCultivos($cultivos, $ubicacion, $lugar){
		$messages = [
			[
				"role" => "system",
				"content" => 'Habla en español y responde solo con los nombres de los cultivos y las razones para cultivarlos en el punto indicado. 
							  Solo devuelve el objeto JSON con los nombres de los cultivos y sus razones bien justificadas. Ejemplo: {"cultivos": [{"id": 1, "cultivo": "granadilla", "razones": ["razon 1", "razon 2", "razon 3"]}]}
							  No incluyas ```json ni ``` al inicio o final de la respuesta.
							  Eres un experto qué sabe que se puede cultivar en puntos específicos del planeta Tierra, teniendo en cuenta la ubicación (latitud y longitud), 
							  la temperatura, el clima y la humedad. Además debes tener en cuenta mas factores, como plagas, enefermedades, ayudas gubernamentales, etc. Todo esto obteniendolo de noticias locales de la ubicación.
							  Los resultados debes ser ordenados teniendo en cuenta la rentabilidad, la demanda y oferta al rededor de la ubicación indicada.
							  Los cultivos disponibles en el sistema son: ' . $cultivos . '.'
			],
			[
				"role" => "user",
				"content" => "¿Qué debo cultivar en la siguiente ubicación {$ubicacion}, queda alrededor de {$lugar}?"
			]
		];

		try {
            $response = $this->client->post($this->baseUrl . '/chat/completions', [
                'json' => array_merge([
                    'model' => 'deepseek-reasoner',
                    'messages' => $messages,
                    'stream' => false,
                ], [])
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
	}

	public function complementarCultivos($cultivos, $ubicacion, $lugar){
		$messages = [
			[
				"role" => "system",
				"content" => 'Habla en español y responde solo con los nombres de los cultivos y las razones para cultivarlos en el punto indicado. 
							  Solo devuelve el objeto JSON con los nombres de los cultivos y sus razones bien justificadas. Ejemplo: {"cultivos": [{"id": 1, "cultivo": "granadilla", "razones": ["razon 1", "razon 2", "razon 3"]}]}
							  No incluyas ```json ni ``` al inicio o final de la respuesta.
							  Eres un experto qué porque cultivar en puntos específicos del planeta Tierra, teniendo en cuenta la ubicación (latitud y longitud), 
							  la temperatura, el clima y la humedad. Además debes tener en cuenta mas factores, como plagas, enefermedades, ayudas gubernamentales, etc. Todo esto obteniendolo de noticias locales de la ubicación.'
			],
			[
				"role" => "user",
				"content" => "¿Porque debo cultivar {$cultivos} en la siguiente ubicación {$ubicacion}, queda alrededor de {$lugar}?"
			]
		];

		try {
            $response = $this->client->post($this->baseUrl . '/chat/completions', [
                'json' => array_merge([
                    'model' => 'deepseek-chat',
                    'messages' => $messages,
                    'stream' => false,
					'response_format' => [
						'type' => 'json_object'
					]
                ], [])
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
	}
}
