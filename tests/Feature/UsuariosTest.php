<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class UsuariosTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_los_usuarios()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/usuarios');

        $response->assertStatus(200);
    }
    public function test_puede_crear_un_usuario()
    {
        $datosUsuario = [
            'email' => 'nuevo_usuario@example.com',
            'password' => 'password123',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'telefono' => '1234567890', // Agrega un número de teléfono válido
            // Otros campos necesarios aquí
        ];
        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/usuarios', $datosUsuario);

        $response->assertStatus(200)
            ->assertJson([
                'mensaje' => 'Usuario creado correctamente'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo_usuario@example.com',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'telefono' => '1234567890', // Asegúrate de coincidir con el número de teléfono proporcionado
            // Otros campos necesarios para la creación del usuario
        ]);
    }

    public function test_puede_mostrar_un_usuario_existente()
    {
        $usuario = User::create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'nombres' => 'John',
            'apellidos' => 'Doe',
            'telefono' => '1234567890', // Proporciona un número de teléfono válido
            // Agrega otros campos necesarios aquí
        ]);
        
        Sanctum::actingAs($this->user);
        $response = $this->getJson("/api/usuarios/{$usuario->id}");

        $response->assertStatus(200)
            ->assertJson([
                'usuario' => $usuario->toArray()
            ]);
    }


    public function test_no_puede_mostrar_un_usuario_inexistente()
    {   
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/usuarios/999');

        $response->assertStatus(404);
    }

    public function test_puede_actualizar_un_usuario_existente()
    {
        $usuario = User::create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'nombres' => 'John',
            'apellidos' => 'Doe',
            'telefono' => '1234567890', // Proporciona un número de teléfono válido
            // Agrega otros campos necesarios aquí
        ]);

        Sanctum::actingAs($this->user);

        $nuevosDatos = [
            'email' => 'nuevoemail@example.com',
            'nombres' => 'Nuevo Nombre',
            'apellidos' => 'Nuevo Apellido',
            // Otros campos necesarios para la actualización
        ];

        $response = $this->putJson("/api/usuarios/{$usuario->id}", $nuevosDatos);

        $response->assertStatus(200)
            ->assertJson([
                'usuario' => $nuevosDatos
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'email' => 'nuevoemail@example.com',
            'nombres' => 'Nuevo Nombre',
            'apellidos' => 'Nuevo Apellido',
            // Otros campos necesarios para la actualización
        ]);
    }


    public function test_puede_eliminar_un_usuario_existente()
    {
        $usuario = User::create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'nombres' => 'John',
            'apellidos' => 'Doe',
            'telefono' => '1234567890', // Proporciona un número de teléfono válido
            // Agrega otros campos necesarios aquí
        ]);
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/usuarios/{$usuario->id}");

        $response->assertStatus(200);

        // $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
    }


    // Agrega más pruebas según sea necesario para cubrir todos los métodos del controlador.
}
