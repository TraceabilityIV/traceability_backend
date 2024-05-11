<?php

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Pais;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class PaisesTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todos_los_paises_autenticado()
    {
        // Simula una solicitud GET a /paises autenticada
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/paises');

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_paises_sin_autenticar()
    {
        // Simula una solicitud GET a /paises sin autenticación
        $response = $this->getJson('/api/paises');

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);
    }

    public function test_puede_crear_pais_autenticado()
    {
        Sanctum::actingAs($this->user);
        // Simula una solicitud POST a /paises autenticada
        $response = $this->postJson('/api/paises', [
            'nombre' => 'Nuevo País',
            'nombre_corto' => 'NP',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1
        ]);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el país se haya creado en la base de datos
        $this->assertDatabaseHas('paises', [
            'nombre' => 'Nuevo País',
            'nombre_corto' => 'NP',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1
        ]);
    }

    public function test_no_puede_crear_pais_sin_autenticar()
    {
        // Simula una solicitud POST a /paises sin autenticación
        $response = $this->postJson('/api/paises', [
            'nombre' => 'Nuevo País',
            'nombre_corto' => 'NP',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1
        ]);

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);

        // Verifica que el país no se haya creado en la base de datos
        $this->assertDatabaseMissing('paises', [
            'nombre' => 'Nuevo País',
            'nombre_corto' => 'NP',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1
        ]);
    }

    public function test_puede_mostrar_un_pais_existente()
    {
        // Crea un nuevo país en la base de datos
        $pais = Pais::create([
            'nombre' => 'Nombre del País',
            'nombre_corto' => 'NP',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /paises/{id} para mostrar un país específico
        $response = $this->getJson('/api/paises/' . $pais->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que los datos del país devueltos coincidan con el país creado
        $response->assertJson([
            'pais' => $pais->toArray()
        ]);
    }

    public function test_no_puede_mostrar_un_pais_inexistente()
    {
        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /paises/{id} para mostrar un país que no existe
        $response = $this->getJson('/api/paises/999');

        // Verifica que se devuelva un código de estado 404 (No encontrado)
        $response->assertStatus(404);
    }

    public function test_puede_actualizar_un_pais_existente()
    {
        // Crear un país en la base de datos
        $pais = Pais::create([
            'nombre' => 'Nombre del País',
            'nombre_corto' => 'NP',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud PUT a /paises/{id} para actualizar un país existente
        $response = $this->putJson('/api/paises/' . $pais->id, [
            'nombre' => 'Nuevo Nombre de País',
        ]);
        
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
        
        // Verifica que el país se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('paises', [
            'id' => $pais->id,
            'nombre' => 'Nuevo Nombre de País',
        ]);
    }

    public function test_puede_eliminar_un_pais_existente()
    {
    // Crear un país en la base de datos
    $pais = Pais::create([
        'nombre' => 'Nombre del País',
        'nombre_corto' => 'NP',
        'indicador' => '+99',
        'codigo_postal' => '99999',
        'estado' => 1
    ]);

    // Autenticar al usuario
    Sanctum::actingAs($this->user);

    // Simula una solicitud DELETE a /paises/{id} para eliminar un país existente
    $response = $this->deleteJson('/api/paises/' . $pais->id);

    // Verifica que se devuelva una respuesta exitosa (código de estado 200)
    $response->assertStatus(200);

    // Verifica que el país ya no exista en la base de datos
    $this->assertDatabaseMissing('paises', [
        'id' => $pais->id
    ]);
    }

}
       
