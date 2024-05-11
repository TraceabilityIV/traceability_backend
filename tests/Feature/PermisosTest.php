<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Permisos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

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

        Sanctum::actingAs($this->user);
    

        $response = $this
                         ->get('/api/permisos');

        $response->assertStatus(200);
    }

    public function test_se_puede_crear_permiso(): void
    {
        Permisos::create([
            'name' => 'Permiso temporal',
            'guard_name' => 'web', // Proporciona un valor válido para guard_name
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->post('/api/permisos', [
            'name' => 'Permiso de prueba',
            'guard_name' => 'web', // Ajusta este valor según sea necesario
        ]);

        $response->assertStatus(200);
    }

    public function test_se_puede_actualizar_permiso(): void
    {
        // Crear un permiso temporalmente
        $permiso = Permisos::create([
            'name' => 'Permiso temporal',
            'guard_name' => 'web', // Proporciona un valor válido para guard_name
        ]);

        // Verificar que se haya creado el permiso
        $this->assertNotNull($permiso);

        // Definir los datos actualizados del permiso
        $datosActualizados = [
            'name' => 'Nuevo Nombre del Permiso',
            'guard_name' => 'web', // Proporciona un valor válido para guard_name
        ];

        // Autenticar al usuario (si es necesario)
        Sanctum::actingAs($this->user);

        // Enviar una solicitud PUT para actualizar el permiso
        $response = $this->putJson("/api/permisos/{$permiso->id}", $datosActualizados);

        // Verificar que la solicitud sea exitosa
        $response->assertStatus(200);

        // Verificar que el permiso se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('permisos', $datosActualizados);
    }

    


    public function test_se_puede_eliminar_permiso(): void
    {
        // Crear un permiso directamente en la base de datos
        $permiso = Permisos::create([
            'name' => 'Nombre del permiso', // Proporciona un nombre válido para el permiso
            'guard_name' => 'web', // Proporciona un valor válido para guard_name
            // Otros campos necesarios aquí
        ]);

        // Verificar que se haya creado el permiso
        $this->assertNotNull($permiso);

        // Autenticar al usuario (si es necesario)
        Sanctum::actingAs($this->user);

        // Enviar una solicitud DELETE para eliminar el permiso
        $response = $this->delete("/api/permisos/{$permiso->id}");

        // Verificar que la solicitud sea exitosa
        $response->assertStatus(200); // 204 No Content

        // Verificar que el permiso haya sido eliminado de la base de datos
        // $this->assertDatabaseMissing('permisos', ['id' => $permiso->id]);
    }

}
