<?php
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Cultivos;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class CultivosTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todos_los_cultivos_autenticado()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/cultivos');

        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_cultivos_sin_autenticar()
    {
        $response = $this->getJson('/api/cultivos');

        $response->assertStatus(401);
    }

    public function test_puede_crear_cultivo_autenticado()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/cultivos', [
            'nombre' => 'Nuevo Cultivo',
            'estado' => 1,
            'ubicacion' => 'Ubicación del cultivo',
            'direccion' => 'Dirección del cultivo',
            'latitud' => 123.456,
            'longitud' => 456.789,
            'fecha_siembra' => '2024-04-10',
            'area' => 100,
            'variedad' => 'Variedad del cultivo',
            'nombre_corto' => 'NC',
            'lote' => 'Lote del cultivo',
            'prefijo_registro' => 'PR',
            'fecha_cosecha' => '2024-04-20',
            'cantidad_aproximada' => 50,
            'usuario_id' => $this->user->id,
            'categoria_id' => 1, // Reemplazar con el ID de la categoría adecuada
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cultivos', [
            'nombre' => 'Nuevo Cultivo',
            'estado' => 1,
            'ubicacion' => 'Ubicación del cultivo',
            'direccion' => 'Dirección del cultivo',
            'latitud' => 123.456,
            'longitud' => 456.789,
            'fecha_siembra' => '2024-04-10',
            'area' => 100,
            'variedad' => 'Variedad del cultivo',
            'nombre_corto' => 'NC',
            'lote' => 'Lote del cultivo',
            'prefijo_registro' => 'PR',
            'fecha_cosecha' => '2024-04-20',
            'cantidad_aproximada' => 50,
            'usuario_id' => $this->user->id,
            'categoria_id' => 1, // Reemplazar con el ID de la categoría adecuada
        ]);
    }

    public function test_no_puede_crear_cultivo_sin_autenticar()
    {
        $response = $this->postJson('/api/cultivos', [
            'nombre' => 'Nuevo Cultivo',
            'estado' => 1,
            'ubicacion' => 'Ubicación del cultivo',
            'direccion' => 'Dirección del cultivo',
            'latitud' => 123.456,
            'longitud' => 456.789,
            'fecha_siembra' => '2024-04-10',
            'area' => 100,
            'variedad' => 'Variedad del cultivo',
            'nombre_corto' => 'NC',
            'lote' => 'Lote del cultivo',
            'prefijo_registro' => 'PR',
            'fecha_cosecha' => '2024-04-20',
            'cantidad_aproximada' => 50,
            'usuario_id' => $this->user->id,
            'categoria_id' => 1, // Reemplazar con el ID de la categoría adecuada
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('cultivos', [
            'nombre' => 'Nuevo Cultivo',
            'estado' => 1,
            'ubicacion' => 'Ubicación del cultivo',
            'direccion' => 'Dirección del cultivo',
            'latitud' => 123.456,
            'longitud' => 456.789,
            'fecha_siembra' => '2024-04-10',
            'area' => 100,
            'variedad' => 'Variedad del cultivo',
            'nombre_corto' => 'NC',
            'lote' => 'Lote del cultivo',
            'prefijo_registro' => 'PR',
            'fecha_cosecha' => '2024-04-20',
            'cantidad_aproximada' => 50,
            'usuario_id' => $this->user->id,
            'categoria_id' => 1, // Reemplazar con el ID de la categoría adecuada
            ]
        );
    }

    public function test_puede_mostrar_un_cultivo_existente()
    {
        $cultivo = Cultivos::create([
            'nombre' => 'Nuevo Cultivo',
            'estado' => 1,
            'ubicacion' => 'Ubicación del cultivo',
            'direccion' => 'Dirección del cultivo',
            'latitud' => 123.456,
            'longitud' => 456.789,
            'fecha_siembra' => '2024-04-10',
            'area' => 100,
            'variedad' => 'Variedad del cultivo',
            'nombre_corto' => 'NC',
            'lote' => 'Lote del cultivo',
            'prefijo_registro' => 'PR',
            'fecha_cosecha' => '2024-04-20',
            'cantidad_aproximada' => 50,
            'usuario_id' => $this->user->id,
            'categoria_id' => 1, // Reemplazar con el ID de la categoría adecuada
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/cultivos/' . $cultivo->id);

        $response->assertStatus(200);
        $response->assertJson([
            'cultivo' => $cultivo->toArray()
        ]);
    }

    public function test_no_puede_mostrar_un_cultivo_inexistente()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/cultivos/999');

        $response->assertStatus(404);
    }

    public function test_puede_actualizar_un_cultivo_existente()
    {
        $cultivo = Cultivos::create([
            'nombre' => 'Nuevo Cultivo',
            'estado' => 1,
            'ubicacion' => 'Ubicación del cultivo',
            'direccion' => 'Dirección del cultivo',
            'latitud' => 123.456,
            'longitud' => 456.789,
            'fecha_siembra' => '2024-04-10',
            'area' => 100,
            'variedad' => 'Variedad del cultivo',
            'nombre_corto' => 'NC',
            'lote' => 'Lote del cultivo',
            'prefijo_registro' => 'PR',
            'fecha_cosecha' => '2024-04-20',
            'cantidad_aproximada' => 50,
            'usuario_id' => $this->user->id,
            'categoria_id' => 1, // Reemplazar con el ID de la categoría adecuada
        ]);

        Sanctum::actingAs($this->user);
        
        $response = $this->putJson('/api/cultivos/' . $cultivo->id, [
            'nombre' => 'Nuevo Nombre de Cultivo',
            // Agregar los campos necesarios para la actualización del cultivo
        ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('cultivos', ['nombre' => 'Nuevo Nombre de Cultivo']);
    }

    public function test_puede_eliminar_un_cultivo_existente()
    {
        $cultivo = Cultivos::create([
            'nombre' => 'Nuevo Cultivo',
            'estado' => 1,
            'ubicacion' => 'Ubicación del cultivo',
            'direccion' => 'Dirección del cultivo',
            'latitud' => 123.456,
            'longitud' => 456.789,
            'fecha_siembra' => '2024-04-10',
            'area' => 100,
            'variedad' => 'Variedad del cultivo',
            'nombre_corto' => 'NC',
            'lote' => 'Lote del cultivo',
            'prefijo_registro' => 'PR',
            'fecha_cosecha' => '2024-04-20',
            'cantidad_aproximada' => 50,
            'usuario_id' => $this->user->id,
            'categoria_id' => 1, // Reemplazar con el ID de la categoría adecuada
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson('/api/cultivos/' . $cultivo->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('cultivos', ['id' => $cultivo->id]);
    }
}
