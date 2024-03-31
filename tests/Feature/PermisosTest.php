<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Permisos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermisosTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_se_puede_listar_permisos(): void
    {
        Permisos::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
                         ->get('/api/permisos');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_se_puede_crear_permiso(): void
    {
        $response = $this->actingAs($this->user)
                         ->post('/api/permisos', ['nombre' => 'Permiso de prueba']);

        $response->assertStatus(201);
    }

    public function test_se_puede_actualizar_permiso(): void
    {
        $permiso = Permisos::factory()->create();

        $response = $this->actingAs($this->user)
                         ->put("/api/permisos/{$permiso->id}", ['nombre' => 'Permiso actualizado']);

        $response->assertStatus(200);
    }

    public function test_se_puede_eliminar_permiso(): void
    {
        $permiso = Permisos::factory()->create();

        $response = $this->actingAs($this->user)
                         ->delete("/api/permisos/{$permiso->id}");

        $response->assertStatus(204);
    }
}
