<?php

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepartamentosTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todos_los_departamentos_autenticado()
    {
        // Simula una solicitud GET a /departamentos autenticada
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/departamentos');

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_departamentos_sin_autenticar()
    {
        // Simula una solicitud GET a /departamentos sin autenticación
        $response = $this->getJson('/api/departamentos');

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);
    }

    public function test_puede_crear_departamento_autenticado()
    {
        Sanctum::actingAs($this->user);
        // Simula una solicitud POST a /departamentos autenticada
        $response = $this->postJson('/api/departamentos', [
            'nombre' => 'Nuevo Departamento',
            'nombre_corto' => 'ND',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'pais_id' => 1, // Reemplaza 1 con el ID del país existente
        ]);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el departamento se haya creado en la base de datos
        $this->assertDatabaseHas('departamentos', [
            'nombre' => 'Nuevo Departamento',
            'nombre_corto' => 'ND',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'pais_id' => 1, // Reemplaza 1 con el ID del país existente
        ]);
    }

    public function test_no_puede_crear_departamento_sin_autenticar()
    {
        // Simula una solicitud POST a /departamentos sin autenticación
        $response = $this->postJson('/api/departamentos', [
            'nombre' => 'Nuevo Departamento',
            'nombre_corto' => 'ND',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'pais_id' => 1, // Reemplaza 1 con el ID del país existente
        ]);

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);

        // Verifica que el departamento no se haya creado en la base de datos
        $this->assertDatabaseMissing('departamentos', [
            'nombre' => 'Nuevo Departamento',
            'nombre_corto' => 'ND',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'pais_id' => 1, // Reemplaza 1 con el ID del país existente
        ]);
    }

    public function test_puede_mostrar_un_departamento_existente()
    {
        // Crea un nuevo departamento en la base de datos
        $departamento = Departamento::create([
            'nombre' => 'Nombre del Departamento',
            'nombre_corto' => 'ND',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'pais_id' => 1, // Reemplaza 1 con el ID del país existente
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /departamentos/{id} para mostrar un departamento específico
        $response = $this->getJson('/api/departamentos/' . $departamento->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que los datos del departamento devueltos coincidan con el departamento creado
        $response->assertJson([
            'departamento' => $departamento->toArray()
        ]);
    }

    public function test_no_puede_mostrar_un_departamento_inexistente()
    {
        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /departamentos/{id} para mostrar un departamento que no existe
        $response = $this->getJson('/api/departamentos/999');

        // Verifica que se devuelva un código de estado 404 (No encontrado)
        $response->assertStatus(404);
    }

    public function test_puede_actualizar_un_departamento_existente()
    {
        // Crear un departamento en la base de datos
        $departamento = Departamento::create([
            'nombre' => 'Departamento existente',
            'nombre_corto' => 'DE',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'pais_id' => 1, // Reemplaza 1 con el ID del país existente
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud PUT a /departamentos/{id} para actualizar un departamento existente
        $response = $this->putJson('/api/departamentos/' . $departamento->id, [
            'nombre' => 'Nuevo Nombre de Departamento',
        ]);
        
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
        
        // Verifica que el departamento se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('departamentos', [
            'id' => $departamento->id,
            'nombre' => 'Nuevo Nombre de Departamento',
        ]);
    }

    public function test_puede_eliminar_un_departamento_existente()
    {
        // Crear un departamento en la base de datos
        $departamento = Departamento::create([
            'nombre' => 'Departamento a eliminar',
            'nombre_corto' => 'DAE',
            'indicador' => '+99',
            'codigo_postal' => '99999',
            'estado' => 1,
            'pais_id' => 1, // Reemplaza 1 con el ID del país existente
        ]);

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud DELETE a /departamentos/{id} para eliminar un departamento existente
        $response = $this->deleteJson('/api/departamentos/' . $departamento->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el departamento ya no exista en la base de datos
        $this->assertDatabaseMissing('departamentos', [
            'id' => $departamento->id
        ]);
    }
}

