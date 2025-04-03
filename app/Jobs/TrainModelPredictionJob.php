<?php

namespace App\Jobs;

use App\Models\CultivosPredefinidos;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrainModelPredictionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public $timeout = 120000;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onConnection('database');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
		// $cultivos = CultivosPredefinidos::select(
		// 	'nombre',
		// 	'temperatura_min as temp_min',
		// 	'temperatura_max as temp_max',
		// 	'ph_min',
		// 	'ph_max',
		// 	'precipitacion_min',
		// 	'profundidad_suelo',
		// 	'textura_suelo'
		// )->get();

		// logger($cultivos);

		$client = new Client();
		$response = $client->request('POST', 'http://ml_service:5000/train', [
			'connect_timeout' => 120000,
			'timeout' => 120000
		]);

		logger($response->getBody()->getContents());

		return;
		
    }
}
