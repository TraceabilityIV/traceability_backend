<?php
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Subagrupadores;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class SubagrupadoresTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todos_los_subagrupadores_autenticado()
    {
        // Simula una solicitud GET a /subagrupadores autenticada
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/subagrupadores');

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_subagrupadores_sin_autenticar()
    {
        // Simula una solicitud GET a /subagrupadores sin autenticación
        $response = $this->getJson('/api/subagrupadores');

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);
    }

    public function test_puede_crear_subagrupador_autenticado()
    {
        Sanctum::actingAs($this->user);
        // Simula una solicitud POST a /subagrupadores autenticada
        $response = $this->postJson('/api/subagrupadores', [
            'nombre' => 'Nuevo Subagrupador',
            'codigo' => 'NS',
            'estado' => 1,
            'agrupador_id' => 1 // Reemplaza con un ID existente de agrupador
        ]);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el subagrupador se haya creado en la base de datos
        $this->assertDatabaseHas('subagrupadores', [
            'nombre' => 'Nuevo Subagrupador',
            'codigo' => 'NS',
            'estado' => 1
        ]);
    }

    public function test_no_puede_crear_subagrupador_sin_autenticar()
    {
        // Simula una solicitud POST a /subagrupadores sin autenticación
        $response = $this->postJson('/api/subagrupadores', [
            'nombre' => 'Nuevo Subagrupador',
            'codigo' => 'NS',
            'estado' => 1,
            'agrupador_id' => 1 // Reemplaza con un ID existente de agrupador
        ]);

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);

        // Verifica que el subagrupador no se haya creado en la base de datos
        $this->assertDatabaseMissing('subagrupadores', [
            'nombre' => 'Nuevo Subagrupador',
            'codigo' => 'NS',
            'estado' => 1
        ]);
    }

    public function test_puede_mostrar_un_subagrupador_existente()
    {
        // Crea un nuevo subagrupador en la base de datos
        $subagrupador = Subagrupadores::create([
            'nombre' => 'Nombre del Subagrupador',
            'codigo' => 'NS',
            'estado' => 1,
            'agrupador_id' => 1 // Reemplaza con un ID existente de agrupador
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /subagrupadores/{id} para mostrar un subagrupador específico
        $response = $this->getJson('/api/subagrupadores/' . $subagrupador->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que los datos del subagrupador devueltos coincidan con el subagrupador creado
        $response->assertJson([
            'subagrupador' => $subagrupador->toArray()
        ]);
    }

    public function test_no_puede_mostrar_un_subagrupador_inexistente()
    {
        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /subagrupadores/{id} para mostrar un subagrupador que no existe
        $response = $this->getJson('/api/subagrupadores/999');

        // Verifica que se devuelva un código de estado 404 (No encontrado)
        $response->assertStatus(404);
    }

    public function test_puede_actualizar_un_subagrupador_existente()
    {
        // Crear un subagrupador en la base de datos
        $subagrupador = Subagrupadores::create([
            'nombre' => 'Nombre del Subagrupador',
            'codigo' => 'NS',
            'estado' => 1,
            'agrupador_id' => 1 // Reemplaza con un ID existente de agrupador
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud PUT a /subagrupadores/{id} para actualizar un subagrupador existente
        $response = $this->putJson('/api/subagrupadores/' . $subagrupador->id, [
            'nombre' => 'Nuevo Nombre de Subagrupador',
        ]);
        
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
        
        // Verifica que el subagrupador se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('subagrupadores', [
            'id' => $subagrupador->id,
            'nombre' => 'Nuevo Nombre de Subagrupador',
        ]);
    }

    public function test_puede_eliminar_un_subagrupador_existente()
    {
        // Crear un subagrupador en la base de datos
        $subagrupador = Subagrupadores::create([
            'nombre' => 'Nombre del Subagrupador',
            'codigo' => 'NS',
            'estado' => 1,
            'agrupador_id' => 1 // Reemplaza con un ID existente de agrupador
        ]);

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud DELETE a /subagrupadores
        $response = $this->deleteJson('/api/subagrupadores/' . $subagrupador->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el subagrupador ya no exista en la base de datos
        $this->assertDatabaseMissing('subagrupadores', [
            'id' => $subagrupador->id
        ]);
    }
}
