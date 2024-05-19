<?php

use App\Models\Ciudad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CiudadesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todas_las_ciudades_autenticado()
    {
        // Simula una solicitud GET a /ciudades autenticada
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/ciudades');

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_ciudades_sin_autenticar()
    {
        // Simula una solicitud GET a /ciudades sin autenticación
        $response = $this->getJson('/api/ciudades');

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);
    }

    public function test_puede_crear_ciudad_autenticado()
    {
        Sanctum::actingAs($this->user);
        // Simula una solicitud POST a /ciudades autenticada
        $response = $this->postJson('/api/ciudades', [
            'nombre' => 'Nueva Ciudad',
            'nombre_corto' => 'NC',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que la ciudad se haya creado en la base de datos
        $this->assertDatabaseHas('ciudades', [
            'nombre' => 'Nueva Ciudad',
            'nombre_corto' => 'NC',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);
    }

    public function test_no_puede_crear_ciudad_sin_autenticar()
    {
        // Simula una solicitud POST a /ciudades sin autenticación
        $response = $this->postJson('/api/ciudades', [
            'nombre' => 'Nueva Ciudad',
            'nombre_corto' => 'NC',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);

        // Verifica que la ciudad no se haya creado en la base de datos
        $this->assertDatabaseMissing('ciudades', [
            'nombre' => 'Nueva Ciudad',
            'nombre_corto' => 'NC',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);
    }

    public function test_puede_mostrar_una_ciudad_existente()
    {
        // Crea una nueva ciudad en la base de datos
        $ciudad = Ciudad::create([
            'nombre' => 'Nueva Ciudad',
            'nombre_corto' => 'NC',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /ciudades/{id} para mostrar una ciudad específica
        $response = $this->getJson('/api/ciudades/' . $ciudad->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que los datos de la ciudad devueltos coincidan con la ciudad creada
        $response->assertJson([
            'ciudad' => $ciudad->toArray()
        ]);
    }

    public function test_no_puede_mostrar_una_ciudad_inexistente()
    {
        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /ciudades/{id} para mostrar una ciudad que no existe
        $response = $this->getJson('/api/ciudades/999');

        // Verifica que se devuelva un código de estado 404 (No encontrado)
        $response->assertStatus(404);
    }

    public function test_puede_actualizar_una_ciudad_existente()
    {
        // Crear una ciudad en la base de datos
        $ciudad = Ciudad::create([
            'nombre' => 'Nueva Ciudad',
            'nombre_corto' => 'NC',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud PUT a /ciudades/{id} para actualizar una ciudad existente
        $response = $this->putJson('/api/ciudades/' . $ciudad->id, [
            'nombre' => 'Nuevo Nombre de Ciudad',
        ]);
                
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
                
        // Verifica que la ciudad se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('ciudades', [
            'id' => $ciudad->id,
            'nombre' => 'Nuevo Nombre de Ciudad',
        ]);
    }
        
    public function test_puede_eliminar_una_ciudad_existente()
    {
        // Crear una ciudad en la base de datos
        $ciudad = Ciudad::create([
            'nombre' => 'Nueva Ciudad',
            'nombre_corto' => 'NC',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);
        
        // Autenticar al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud DELETE a /ciudades/{id} para eliminar una ciudad existente
        $response = $this->deleteJson('/api/ciudades/' . $ciudad->id);
        
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
        
        // Verifica que la ciudad ya no exista en la base de datos
        $this->assertDatabaseMissing('ciudades', [
            'id' => $ciudad->id
        ]);
    }
}
        
