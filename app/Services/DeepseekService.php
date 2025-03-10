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
}
