<?php

    use App\Http\Controllers\Api\EvidenciasController;
    use App\Http\Requests\Evidencias\ActualizarRequest;
    use App\Http\Requests\Evidencias\CrearRequest;
    use App\Http\Requests\Evidencias\ObtenerRequest;
    use App\Models\Evidencia;
    use App\Models\TrazabilidadCultivo;
    use App\Models\User;
    use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Laravel\Sanctum\Sanctum;
    use Tests\TestCase;
    use Illuminate\Http\UploadedFile;
    
    class EvidenciasTest extends TestCase
    {
        use RefreshDatabase, InteractsWithDatabase;
    
        protected $user;
    
        protected function setUp(): void
        {
            parent::setUp();
            
            $this->seed();
    
            $this->user = User::factory()->create();
        }
    
        public function test_puede_obtener_todas_las_evidencias_de_una_trazabilidad_autenticado()
        {
            Sanctum::actingAs($this->user);
            
            $trazabilidad = TrazabilidadCultivo::create([
                // Coloca aquí los datos necesarios para crear una trazabilidad de cultivo
            ]);
    
            $response = $this->getJson('/api/evidencias?trazabilidad_cultivos_id=' . $trazabilidad->id);
    
            $response->assertStatus(200);
        }
    
        public function test_no_puede_obtener_evidencias_de_una_trazabilidad_sin_autenticar()
        {
            $trazabilidad = TrazabilidadCultivo::create([
                'cultivo_id' => 1,
                'aplicacion' => 'Aplicacion de prueba',
                'descripcion' => 'Descripcion de prueba',
                'resultados' => 'Resultados de prueba'
            
            ]);
    
            $response = $this->getJson('/api/evidencias?trazabilidad_cultivos_id=' . $trazabilidad->id);
    
            $response->assertStatus(401);
        }
    
        public function test_puede_crear_una_evidencia_autenticado()
        {
            Sanctum::actingAs($this->user);
    
            $trazabilidad = TrazabilidadCultivo::create([
                'cultivo_id' => 1,
                'aplicacion' => 'Aplicacion de prueba',
                'descripcion' => 'Descripcion de prueba',
                'resultados' => 'Resultados de prueba'
            
            ]);
    
            $response = $this->postJson('/api/evidencias', [
                'descripcion' => 'Descripción de la evidencia',
                'trazabilidad_cultivos_id' => $trazabilidad->id,
                'evidencia' => UploadedFile::fake()->image('evidencia.jpg')
            ]);
    
            $response->assertStatus(200);
    
            $this->assertDatabaseHas('evidencias', [
                'descripcion' => 'Descripción de la evidencia',
                'trazabilidad_cultivos_id' => $trazabilidad->id,
            ]);
        }
    
        public function test_no_puede_crear_una_evidencia_sin_autenticar()
        {
            $trazabilidad = TrazabilidadCultivo::create([
                'cultivo_id' => 1,
                'aplicacion' => 'Aplicacion de prueba',
                'descripcion' => 'Descripcion de prueba',
                'resultados' => 'Resultados de prueba'
            
            ]);
    
            $response = $this->postJson('/api/evidencias', [
                'descripcion' => 'Descripción de la evidencia',
                'trazabilidad_cultivos_id' => $trazabilidad->id,
                'evidencia' => UploadedFile::fake()->image('evidencia.jpg')
            ]);
    
            $response->assertStatus(401);
    
            $this->assertDatabaseMissing('evidencias', [
                'descripcion' => 'Descripción de la evidencia',
                'trazabilidad_cultivos_id' => $trazabilidad->id,
            ]);
        }
    
        public function test_puede_mostrar_una_evidencia_existente()
        {
            Sanctum::actingAs($this->user);
            $trazabilidad = TrazabilidadCultivo::create([
                'cultivo_id' => 1,
                'aplicacion' => 'Aplicacion de prueba',
                'descripcion' => 'Descripcion de prueba',
                'resultados' => 'Resultados de prueba'
            
            ]);
    
            $evidencia = Evidencia::create([
                'descripcion' => 'Descripción de la evidencia',
                'trazabilidad_cultivos_id' => $trazabilidad->id,
                'evidencia' => UploadedFile::fake()->image('evidencia.jpg')
            ]);
    
            $response = $this->getJson('/api/evidencias/' . $evidencia->id);
    
            $response->assertStatus(200);
    
            $response->assertJson([
                'evidencia' => $evidencia->toArray()
            ]);
        }
    
        public function test_no_puede_mostrar_una_evidencia_inexistente()
        {
            Sanctum::actingAs($this->user);
    
            $response = $this->getJson('/api/evidencias/999');
    
            $response->assertStatus(404);
        }
    
        public function test_puede_actualizar_una_evidencia_existente()
        {
            Sanctum::actingAs($this->user);

            $trazabilidad = TrazabilidadCultivo::create([
                'cultivo_id' => 1,
                'aplicacion' => 'Aplicacion de prueba',
                'descripcion' => 'Descripcion de prueba',
                'resultados' => 'Resultados de prueba'
            
            ]);
    
            $evidencia = Evidencia::create([
                'descripcion' => 'Descripción de la evidencia',
                'trazabilidad_cultivos_id' => $trazabilidad->id,
                'evidencia' => UploadedFile::fake()->image('evidencia.jpg')
            ]);
    
            $response = $this->putJson('/api/evidencias/' . $evidencia->id, [
                'descripcion' => 'Nueva descripción de la evidencia',
                'evidencia' => UploadedFile::fake()->image('evidencia.jpg')
            ]);
    
            $response->assertStatus(200);
    
            $this->assertDatabaseHas('evidencias', [
                'id' => $evidencia->id,
                'descripcion' => 'Nueva descripción de la evidencia',
            ]);
        }
    
        public function test_puede_eliminar_una_evidencia_existente()
        {
            Sanctum::actingAs($this->user);

            $trazabilidad = TrazabilidadCultivo::create([
                'cultivo_id' => 1,
                'aplicacion' => 'Aplicacion de prueba',
                'descripcion' => 'Descripcion de prueba',
                'resultados' => 'Resultados de prueba'
            
            ]);
    
            $evidencia = Evidencia::create([
                'descripcion' => 'Descripción de la evidencia',
                'trazabilidad_cultivos_id' => $trazabilidad->id,
                'evidencia' => UploadedFile::fake()->image('evidencia.jpg')
            ]);
    
            $response = $this->deleteJson('/api/evidencias/' . $evidencia->id);
    
            $response->assertStatus(200);
    
            $this->assertDatabaseMissing('evidencias', ['id' => $evidencia->id]);
        }
    
        public function test_no_puede_eliminar_una_evidencia_inexistente()
        {
            Sanctum::actingAs($this->user);
    
            $response = $this->deleteJson('/api/evidencias/999');
    
            $response->assertStatus(404);
        }
    }
    