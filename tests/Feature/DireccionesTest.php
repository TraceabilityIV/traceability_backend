<?php
use App\Models\Direcciones;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DireccionesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todas_las_direcciones_autenticado()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/direcciones');

        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_direcciones_sin_autenticar()
    {
        $response = $this->getJson('/api/direcciones');

        $response->assertStatus(401);
    }

    public function test_puede_crear_direccion_autenticado()
    {
        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/direcciones', [
            'direccion' => 'Calle Ejemplo 123',
            'receptor' => 'Juan Perez',
            'latitud' => 123.456,
            'longitud' => -78.910,
            'barrio_id' => 1, // Reemplaza 1 con el ID del barrio existente
            'usuario_id' => $this->user->id,
            'estado' => 1,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('direcciones', [
            'direccion' => 'Calle Ejemplo 123',
            'receptor' => 'Juan Perez',
            'latitud' => 123.456,
            'longitud' => -78.910,
            'barrio_id' => 1,
            'usuario_id' => $this->user->id,
            'estado' => 1,
        ]);
    }

    public function test_no_puede_crear_direccion_sin_autenticar()
    {
        $response = $this->postJson('/api/direcciones', [
            'direccion' => 'Calle Ejemplo 123',
            'receptor' => 'Juan Perez',
            'latitud' => 123.456,
            'longitud' => -78.910,
            'barrio_id' => 1,
            'usuario_id' => $this->user->id,
            'estado' => 1,
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('direcciones', [
            'direccion' => 'Calle Ejemplo 123',
            'receptor' => 'Juan Perez',
            'latitud' => 123.456,
            'longitud' => -78.910,
            'barrio_id' => 1,
            'usuario_id' => $this->user->id,
            'estado' => 1,
        ]);
    }

    public function test_puede_mostrar_una_direccion_existente()
    {
        $direccion = Direcciones::create([
            'direccion' => 'Calle Ejemplo 123',
            'receptor' => 'Juan Perez',
            'latitud' => 123.456,
            'longitud' => -78.910,
            'barrio_id' => 1, // Reemplaza 1 con el ID del barrio existente
            'usuario_id' => $this->user->id,
            'estado' => 1,
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/direcciones/' . $direccion->id);

        $response->assertStatus(200);
        $response->assertJson([
            'direccion' => $direccion->toArray(),
        ]);
    }

    public function test_no_puede_mostrar_una_direccion_inexistente()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/direcciones/999');

        $response->assertStatus(404);
    }

    public function test_puede_actualizar_una_direccion_existente()
    {
        $direccion = Direcciones::create([
            'direccion' => 'Calle Ejemplo 123',
            'receptor' => 'Juan Perez',
            'latitud' => 123.456,
            'longitud' => -78.910,
            'barrio_id' => 1, // Reemplaza 1 con el ID del barrio existente
            'usuario_id' => $this->user->id,
            'estado' => 1,
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->putJson('/api/direcciones/' . $direccion->id, [
            'direccion' => 'Nueva Calle 456',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('direcciones', [
            'id' => $direccion->id,
            'direccion' => 'Nueva Calle 456',
        ]);
    }

    public function test_puede_eliminar_una_direccion_existente()
    {
        $direccion = Direcciones::create([
            'direccion' => 'Calle Ejemplo 123',
            'receptor' => 'Juan Perez',
            'latitud' => 123.456,
            'longitud' => -78.910,
            'barrio_id' => 1, // Reemplaza 1 con el ID del barrio existente
            'usuario_id' => $this->user->id,
            'estado' => 1,
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->deleteJson('/api/direcciones/' . $direccion->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('direcciones', [
            'id' => $direccion->id,
        ]);
    }
}
