<?php

namespace App\Jobs;

use App\Models\Cultivos;
use App\Models\TrazabilidadCultivo;
use App\Services\DeepseekService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResumenTrazabilidadCultivosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $timeout = 120000;
    public $cultivoId;

    public function __construct($cultivoId)
    {
        $this->cultivoId = $cultivoId;
		$this->onConnection('database');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cultivo = Cultivos::find($this->cultivoId);

        if($cultivo == null){
            return;
        }

		$trazabilidades = TrazabilidadCultivo::where('cultivo_id', $this->cultivoId)
		->with([
			'evidencias' => function($query) {
				$query->select('id', 'trazabilidad_cultivos_id', 'descripcion');
			}
		])
		->select('id', 'aplicacion', 'descripcion', 'resultados', 'fecha_aplicacion', 'ultima_revision', 'cultivo_id')
		->get();

		$res = app(DeepseekService::class)->resumenTrazabilidad(json_encode($trazabilidades));

		$json_string = $res["choices"][0]['message']['content'] ?? ""; 

		logger($json_string);

		$data = json_decode($json_string, true);

		logger($data);

		$cultivo->resumen = $data;
		$cultivo->save();
    }
}
