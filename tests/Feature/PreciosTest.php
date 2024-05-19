<?php
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Precio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class PreciosTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    // Pruebas de obtención de precios

    public function test_puede_obtener_todos_los_precios_autenticado()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/precios');

        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_precios_sin_autenticar()
    {
        $response = $this->getJson('/api/precios');

        $response->assertStatus(401);
    }

    // Pruebas de creación de precios

    public function test_puede_crear_precio_autenticado()
    {
        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/precios', [
            'precio_venta' => 10.99,
            'cultivo_id' => 1,
            'tipo_id' => 1,
            'estado' => 1
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('precios', [
            'precio_venta' => 10.99,
            'cultivo_id' => 1,
            'tipo_id' => 1,
            'estado' => 1
        ]);
    }

    public function test_no_puede_crear_precio_sin_autenticar()
    {
        $response = $this->postJson('/api/precios', [
            'precio_venta' => 10.99,
            'cultivo_id' => 1,
            'tipo_id' => 1,
            'estado' => 1
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('precios', [
            'precio_venta' => 10.99,
            'cultivo_id' => 1,
            'tipo_id' => 1,
            'estado' => 1
        ]);
    }

    // Pruebas de mostrar un precio existente

    public function test_puede_mostrar_un_precio_existente()
    {
        $precio = Precio::create([
            'precio_venta' => 10.99,
            'cultivo_id' => 1,
            'tipo_id' => 1,
            'estado' => 1
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/precios/' . $precio->id);

        $response->assertStatus(200);

        $response->assertJson([
            'precio' => $precio->toArray()
        ]);
    }

    public function test_no_puede_mostrar_un_precio_inexistente()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/precios/999');

        $response->assertStatus(404);
    }

    // Pruebas de actualización de un precio existente

    public function test_puede_actualizar_un_precio_existente()
    {
        $precio = Precio::create([
            'precio_venta' => 10.99,
            'cultivo_id' => 1,
            'tipo_id' => 1,
            'estado' => 1
        ]);

        Sanctum::actingAs($this->user);
        
        $response = $this->putJson('/api/precios/' . $precio->id, [
            'precio_venta' => 15.99,
            'tipo_id' => 2,
            'estado' => 0
        ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('precios', [
            'id' => $precio->id,
            'precio_venta' => 15.99,
            'tipo_id' => 2,
            'estado' => 0
        ]);
    }

    // Pruebas de eliminación de un precio existente

    public function test_puede_eliminar_un_precio_existente()
    {
        $precio = Precio::create([
            'precio_venta' => 10.99,
            'cultivo_id' => 1,
            'tipo_id' => 1,
            'estado' => 1
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson('/api/precios/' . $precio->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('precios', ['id' => $precio->id]);
    }
}
