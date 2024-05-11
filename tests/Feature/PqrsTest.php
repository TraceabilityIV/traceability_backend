<?php
use App\Models\Pqr;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PqrsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todas_las_pqrs_autenticado()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/pqrs');

        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_pqrs_sin_autenticar()
    {
        $response = $this->getJson('/api/pqrs');

        $response->assertStatus(401);
    }

    public function test_puede_crear_pqr_autenticado()
    {
        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/pqrs', [
            'nombres' => 'Juan Perez',
            'correo' => 'juan@example.com',
            'telefono' => '123456789',
            'direccion' => 'Calle Ejemplo 123',
            'asunto' => 'Reporte de problema',
            'descripcion' => 'El agua no llega correctamente a mi casa',
            'usuario_id' => $this->user->id,
            'barrio_id' => 1, // Reemplaza 1 con el ID del barrio existente
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('pqrs', [
            'nombres' => 'Juan Perez',
            'correo' => 'juan@example.com',
            'telefono' => '123456789',
            'direccion' => 'Calle Ejemplo 123',
            'asunto' => 'Reporte de problema',
            'descripcion' => 'El agua no llega correctamente a mi casa',
            'usuario_id' => $this->user->id,
            'barrio_id' => 1,
        ]);
    }

    public function test_no_puede_crear_pqr_sin_autenticar()
    {
        $response = $this->postJson('/api/pqrs', [
            'nombres' => 'Juan Perez',
            'correo' => 'juan@example.com',
            'telefono' => '123456789',
            'direccion' => 'Calle Ejemplo 123',
            'asunto' => 'Reporte de problema',
            'descripcion' => 'El agua no llega correctamente a mi casa',
            'usuario_id' => $this->user->id,
            'barrio_id' => 1,
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('pqrs', [
            'nombres' => 'Juan Perez',
            'correo' => 'juan@example.com',
            'telefono' => '123456789',
            'direccion' => 'Calle Ejemplo 123',
            'asunto' => 'Reporte de problema',
            'descripcion' => 'El agua no llega correctamente a mi casa',
            'usuario_id' => $this->user->id,
            'barrio_id' => 1,
        ]);
    }

    public function test_puede_mostrar_un_pqr_existente()
    {
        // Crea un nuevo PQR en la base de datos
        $pqr = Pqr::create([
            'nombres' => 'Juan Perez',
            'correo' => 'juan@example.com',
            'telefono' => '123456789',
            'direccion' => 'Calle Ejemplo 123',
            'asunto' => 'Reporte de problema',
            'descripcion' => 'El agua no llega correctamente a mi casa',
            'usuario_id' => $this->user->id,
            'barrio_id' => 1,
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /pqrs/{id} para mostrar un PQR específico
        $response = $this->getJson('/api/pqrs/' . $pqr->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que los datos del PQR devueltos coincidan con el PQR creado
        $response->assertJson([
            'pqr' => $pqr->toArray()
        ]);
    }

    public function test_no_puede_mostrar_un_pqr_inexistente()
    {
        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /pqrs/{id} para mostrar un PQR que no existe
        $response = $this->getJson('/api/pqrs/999');

        // Verifica que se devuelva un código de estado 404 (No encontrado)
        $response->assertStatus(404);
    }

    public function test_puede_actualizar_un_pqr_existente()
    {
        // Crear un PQR en la base de datos
        $pqr = Pqr::create([
            'nombres' => 'Juan Perez',
            'correo' => 'juan@example.com',
            'telefono' => '123456789',
            'direccion' => 'Calle Ejemplo 123',
            'asunto' => 'Reporte de problema',
            'descripcion' => 'El agua no llega correctamente a mi casa',
            'usuario_id' => $this->user->id,
            'barrio_id' => 1,
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud PUT a /pqrs/{id} para actualizar un PQR existente
        $response = $this->putJson('/api/pqrs/' . $pqr->id, [
            'asunto' => 'Nuevo Asunto',
        ]);
        
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
        
        // Verifica que el PQR se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('pqrs', [
            'id' => $pqr->id,
            'asunto' => 'Nuevo Asunto',
        ]);
    }

    public function test_puede_eliminar_un_pqr_existente()
    {
        // Crear un PQR en la base de datos
        $pqr = Pqr::create([
            'nombres' => 'Juan Perez',
            'correo' => 'juan@example.com',
            'telefono' => '123456789',
            'direccion' => 'Calle Ejemplo 123',
            'asunto'         => 'Reporte de problema',
            'descripcion' => 'El agua no llega correctamente a mi casa',
            'usuario_id' => $this->user->id,
            'barrio_id' => 1,
        ]);

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud DELETE a /pqrs/{id} para eliminar un PQR existente
        $response = $this->deleteJson('/api/pqrs/' . $pqr->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el PQR ya no exista en la base de datos
        $this->assertDatabaseMissing('pqrs', [
            'id' => $pqr->id
        ]);
    }
}
