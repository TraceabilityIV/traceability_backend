<?php
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Agrupador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class AgrupadoresTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todos_los_agrupadores_autenticado()
    {
        // Simula una solicitud GET a /agrupadores autenticada
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/agrupadores');

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_agrupadores_sin_autenticar()
    {
        // Simula una solicitud GET a /agrupadores sin autenticación
        $response = $this->getJson('/api/agrupadores');

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);
    }

    public function test_puede_crear_agrupador_autenticado()
    {
        Sanctum::actingAs($this->user);
        // Simula una solicitud POST a /agrupadores autenticada
        $response = $this->postJson('/api/agrupadores', [
            'nombre' => 'Nuevo Agrupador',
            'codigo' => 'NA',
            'estado' => 1
        ]);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el agrupador se haya creado en la base de datos
        $this->assertDatabaseHas('agrupadores', [
            'nombre' => 'Nuevo Agrupador',
            'codigo' => 'NA',
            'estado' => 1
        ]);
    }

    public function test_no_puede_crear_agrupador_sin_autenticar()
    {
        // Simula una solicitud POST a /agrupadores sin autenticación
        $response = $this->postJson('/api/agrupadores', [
            'nombre' => 'Nuevo Agrupador',
            'codigo' => 'NA',
            'estado' => 1
        ]);

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);

        // Verifica que el agrupador no se haya creado en la base de datos
        $this->assertDatabaseMissing('agrupadores', [
            'nombre' => 'Nuevo Agrupador',
            'codigo' => 'NA',
            'estado' => 1
        ]);
    }

    public function test_puede_mostrar_un_agrupador_existente()
    {
        // Crea un nuevo agrupador en la base de datos
        $agrupador = Agrupador::create([
            'nombre' => 'Nombre del Agrupador',
            'codigo' => 'NA',
            'estado' => 1
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /agrupadores/{id} para mostrar un agrupador específico
        $response = $this->getJson('/api/agrupadores/' . $agrupador->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que los datos del agrupador devueltos coincidan con el agrupador creado
        $response->assertJson([
            'agrupador' => $agrupador->toArray()
        ]);
    }

    public function test_puede_actualizar_un_agrupador_existente()
    {
        // Crear un agrupador en la base de datos
        $agrupador = Agrupador::create([
            'nombre' => 'Nombre del Agrupador',
            'codigo' => 'NA',
            'estado' => 1
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud PUT a /agrupadores/{id} para actualizar un agrupador existente
        $response = $this->putJson('/api/agrupadores/' . $agrupador->id, [
            'nombre' => 'Nuevo Nombre de Agrupador',
            'codigo' => 'NN',
            'estado' => 0
        ]);
        
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
        
        // Verifica que el agrupador se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('agrupadores', [
            'id' => $agrupador->id,
            'nombre' => 'Nuevo Nombre de Agrupador',
            'codigo' => 'NN',
            'estado' => 0
        ]);
    }

    public function test_puede_eliminar_un_agrupador_existente()
    {
        // Crear un agrupador en la base de datos
        $agrupador = Agrupador::create([
            'nombre' => 'Nombre del Agrupador',
            'codigo' => 'NA',
            'estado' => 1
        ]);

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud DELETE a /agrupadores/{id} para eliminar un agrupador existente
        $response = $this->deleteJson('/api/agrupadores/' . $agrupador->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el agrupador ya no exista en la base de datos
        $this->assertDatabaseMissing('agrupadores', [
            'id' => $agrupador->id
        ]);
    }
}

        
