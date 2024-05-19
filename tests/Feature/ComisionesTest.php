<?php

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Comision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ComisionesTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todas_las_comisiones_autenticado()
    {
        // Simula una solicitud GET a /comisiones autenticada
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/comisiones');

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_comisiones_sin_autenticar()
    {
        // Simula una solicitud GET a /comisiones sin autenticación
        $response = $this->getJson('/api/comisiones');

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);
    }

    public function test_puede_crear_comision_autenticado()
    {
        Sanctum::actingAs($this->user);
        // Simula una solicitud POST a /comisiones autenticada
        $response = $this->postJson('/api/comisiones', [
            'nombre' => 'Nueva Comisión',
            'porcentaje' => 10,
            'estado' => 1
        ]);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que la comisión se haya creado en la base de datos
        $this->assertDatabaseHas('comisions', [
            'nombre' => 'Nueva Comisión',
            'porcentaje' => 10,
            'estado' => 1
        ]);
    }

    public function test_no_puede_crear_comision_sin_autenticar()
    {
        // Simula una solicitud POST a /comisiones sin autenticación
        $response = $this->postJson('/api/comisiones', [
            'nombre' => 'Nueva Comisión',
            'porcentaje' => 10,
            'estado' => 1
        ]);

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);

        // Verifica que la comisión no se haya creado en la base de datos
        $this->assertDatabaseMissing('comisions', [
            'nombre' => 'Nueva Comisión',
            'porcentaje' => 10,
            'estado' => 1
        ]);
    }

    public function test_puede_mostrar_una_comision_existente()
    {
        // Crea una nueva comisión en la base de datos
        $comision = Comision::factory()->create();

        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /comisiones/{id} para mostrar una comisión específica
        $response = $this->getJson('/api/comisiones/' . $comision->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que los datos de la comisión devueltos coincidan con la comisión creada
        $response->assertJson([
            'comision' => $comision->toArray()
        ]);
    }

    public function test_no_puede_mostrar_una_comision_inexistente()
    {
        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /comisiones/{id} para mostrar una comisión que no existe
        $response = $this->getJson('/api/comisiones/999');

        // Verifica que se devuelva un código de estado 404 (No encontrado)
        $response->assertStatus(404);
    }

    public function test_puede_actualizar_una_comision_existente()
    {
        // Crear una comisión en la base de datos
        $comision = Comision::factory()->create();

        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud PUT a /comisiones/{id} para actualizar una comisión existente
        $response = $this->putJson('/api/comisiones/' . $comision->id, [
            'nombre' => 'Nueva Comisión Actualizada',
            'porcentaje' => 20
        ]);
        
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
        
        // Verifica que la comisión se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('comisions', [
            'id' => $comision->id,
            'nombre' => 'Nueva Comisión Actualizada',
            'porcentaje' => 20
        ]);
    }
    
    public function test_puede_eliminar_una_comision_existente()
    {
        // Crear una comisión en la base de datos
        $comision = Comision::factory()->create();

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud DELETE a /comisiones/{id} para eliminar una comisión existente
        $response = $this->deleteJson('/api/comisiones/' . $comision->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que la comisión ya no exista en la base de datos
        $this->assertDatabaseMissing('comisions', [
            'id' => $comision->id
        ]);
    }
}

