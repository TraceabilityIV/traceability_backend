<?php
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Cultivos;
use App\Models\TrazabilidadCultivo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class TrazabilidadCultivosTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todas_las_trazabilidades_de_un_cultivo_autenticado()
    {
        Sanctum::actingAs($this->user);
        
        $response = $this->getJson('/api/trazabilidades-cultivos?cultivo_id=1');

        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_trazabilidades_de_un_cultivo_sin_autenticar()
    {
        $response = $this->getJson('/api/trazabilidades-cultivos?cultivo_id=1');

        $response->assertStatus(401);
    }

    public function test_puede_crear_una_trazabilidad_autenticado()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/trazabilidades-cultivos', [
            'cultivo_id' => 1,
            'aplicacion' => 'Aplicacion de prueba',
            'descripcion' => 'Descripcion de prueba',
            'resultados' => 'Resultados de prueba',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('trazabilidades_cultivos', [
            'cultivo_id' => 1,
            'aplicacion' => 'Aplicacion de prueba',
            'descripcion' => 'Descripcion de prueba',
            'resultados' => 'Resultados de prueba',
            'usuario_id' => $this->user->id,
        ]);
    }

    public function test_no_puede_crear_una_trazabilidad_sin_autenticar()
    {
        $response = $this->postJson('/api/trazabilidades-cultivos', [
            'cultivo_id' => 1,
            'aplicacion' => 'Aplicacion de prueba',
            'descripcion' => 'Descripcion de prueba',
            'resultados' => 'Resultados de prueba',
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('trazabilidades_cultivos', [
            'cultivo_id' => 1,
            'aplicacion' => 'Aplicacion de prueba',
            'descripcion' => 'Descripcion de prueba',
            'resultados' => 'Resultados de prueba',
        ]);
    }

    public function test_puede_mostrar_una_trazabilidad_existente()
    {
        Sanctum::actingAs($this->user);

        $trazabilidad = TrazabilidadCultivo::create([
            'cultivo_id' => 1,
        ]);

        $response = $this->getJson('/api/trazabilidades-cultivos/' . $trazabilidad->id);

        $response->assertStatus(200);

        $response->assertJson([
            'trazabilidad' => $trazabilidad->toArray()
        ]);
    }

    public function test_no_puede_mostrar_una_trazabilidad_inexistente()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/trazabilidades-cultivos/999');

        $response->assertStatus(404);
    }

    public function test_puede_actualizar_una_trazabilidad_existente()
    {
        Sanctum::actingAs($this->user);

        $trazabilidad = TrazabilidadCultivo::create([
            'cultivo_id' => 1,
            'aplicacion' => 'Aplicacion de prueba',
            'descripcion' => 'Descripcion de prueba',
            'resultados' => 'Resultados de prueba',
        ]);

        $response = $this->putJson('/api/trazabilidades-cultivos/' . $trazabilidad->id, [
            'aplicacion' => 'Nueva Aplicacion',
            'descripcion' => 'Nueva Descripcion',
            'resultados' => 'Nuevos Resultados',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('trazabilidades_cultivos', [
            'id' => $trazabilidad->id,
            'aplicacion' => 'Nueva Aplicacion',
            'descripcion' => 'Nueva Descripcion',
            'resultados' => 'Nuevos Resultados',
        ]);
    }

    public function test_puede_eliminar_una_trazabilidad_existente()
    {
        Sanctum::actingAs($this->user);

        $trazabilidad = TrazabilidadCultivo::create([
            'cultivo_id' => 1,
        ]);

        $response = $this->deleteJson('/api/trazabilidades-cultivos/' . $trazabilidad->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('trazabilidades_cultivos', ['id' => $trazabilidad->id]);
    }

    public function test_no_puede_eliminar_una_trazabilidad_inexistente()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson('/api/trazabilidades-cultivos/999');

        $response->assertStatus(404);
    }
}
