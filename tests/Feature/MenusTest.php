<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Menu;


class MenusTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    /**
     * A basic feature test example.
     */
    public function test_se_puede_acceder_a_obtener_menus(): void
    {

        $response = $this->actingAs($this->user)
                                    ->get('/api/menu');

        $response->assertStatus(200);
    }

    public function test_se_puede_obtener_menus(): void
    {
        $response = $this->actingAs($this->user)
                                    ->get('/api/menu');

        $response->assertJsonFragment([
            'data' => []
        ]);
    }

    public function test_se_puede_crear_menus(): void
    {
        $this->withExceptionHandling();
        $response = $this->actingAs($this->user)
                                    ->post('/api/menu', [
                                        'nombre' => 'menu',
                                    ]);

        $response->assertStatus(200);
    }

    public function test_se_puede_actualizar_menu(): void
    {
        $menu = Menu::factory()->create();

        $response = $this->actingAs($this->user)
                        ->put("/api/menu/{$menu->id}", ['nombre' => 'Nuevo Nombre']);

        $response->assertStatus(200);
    }

    public function test_se_puede_eliminar_menu(): void
    {
        $menu = Menu::factory()->create();

        $response = $this->actingAs($this->user)
                        ->delete("/api/menu/{$menu->id}");

        $response->assertStatus(204); // 204 No Content

        // Verifica que el menú haya sido eliminado de la base de datos
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }

}
