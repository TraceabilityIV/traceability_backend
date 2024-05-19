<?php

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\CostosEnvio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class CostosEnviosTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todos_los_costos_envios_autenticado()
    {
        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud GET a /costos_envios autenticada
        $response = $this->getJson('/api/costos_envios');

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_costos_envios_sin_autenticar()
    {
        // Simula una solicitud GET a /costos_envios sin autenticación
        $response = $this->getJson('/api/costos_envios');

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);
    }

    public function test_puede_crear_costo_envio_autenticado()
    {
        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud POST a /costos_envios autenticada
        $response = $this->postJson('/api/costos_envios', [
            'costo' => 10.00,
            'estado' => 1,
            'tipo_costo_id' => 1,
            'categorias' => [1, 2, 3] // Suponiendo que estas son las IDs de las categorías asociadas
        ]);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el costo de envío se haya creado en la base de datos
        $this->assertDatabaseHas('costos_envios', [
            'costo' => 10.00,
            'estado' => 1,
            'tipo_costo_id' => 1,
        ]);
    }

    public function test_no_puede_crear_costo_envio_sin_autenticar()
    {
        // Simula una solicitud POST a /costos_envios sin autenticación
        $response = $this->postJson('/api/costos_envios', [
            'costo' => 10.00,
            'estado' => 1,
            'tipo_costo_id' => 1,
            'categorias' => [1, 2, 3] // Suponiendo que estas son las IDs de las categorías asociadas
        ]);

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);

        // Verifica que el costo de envío no se haya creado en la base de datos
        $this->assertDatabaseMissing('costos_envios', [
            'costo' => 10.00,
            'estado' => 1,
            'tipo_costo_id' => 1,
        ]);
    }

    public function test_puede_mostrar_un_costo_envio_existente()
    {
        // Crea un nuevo costo de envío en la base de datos
        $costoEnvio = CostosEnvio::factory()->create();

        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /costos_envios/{id} para mostrar un costo de envío específico
        $response = $this->getJson('/api/costos_envios/' . $costoEnvio->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que los datos del costo de envío devueltos coincidan con el costo de envío creado
        $response->assertJson([
            'costo' => $costoEnvio->costo,
            'estado' => $costoEnvio->estado,
            'tipo_costo_id' => $costoEnvio->tipo_costo_id,
        ]);
    }

    public function test_no_puede_mostrar_un_costo_envio_inexistente()
    {
        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /costos_envios/{id} para mostrar un costo de envío que no existe
        $response = $this->getJson('/api/costos_envios/999');

        // Verifica que se devuelva un código de estado 404 (No encontrado)
        $response->assertStatus(404);
    }

    public function test_puede_actualizar_un_costo_envio_existente()
    {
        // Crear un costo de envío en la base de datos
        $costoEnvio = CostosEnvio::factory()->create();

        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud PUT a /costos_envios/{id} para actualizar un costo de envío existente
        $response = $this->putJson('/api/costos_envios/' . $costoEnvio->id, [
            'costo' => 20.00,
            'estado' => 0,
            'tipo_costo_id' => 2,
            'categorias' => [4, 5, 6] // Suponiendo que estas son las IDs de las categorías asociadas
        ]);
        
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
        
        // Verifica que el costo de envío se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('costos_envios', [
            'id' => $costoEnvio->id,
            'costo' => 20.00,
            'estado' => 0,
            'tipo_costo_id' => 2,
        ]);
    }

    public function test_puede_eliminar_un_costo_envio_existente()
    {
        // Crear un costo de envío en la base de datos
        $costoEnvio = CostosEnvio::factory()->create();

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud DELETE a /costos_envios/{id} para eliminar un costo de envío existente
        $response = $this->deleteJson('/api/costos_envios/' . $costoEnvio->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el costo de envío ya no exista en la base de datos
        $this->assertDatabaseMissing('costos_envios', [
            'id' => $costoEnvio->id
        ]);
    }
}

