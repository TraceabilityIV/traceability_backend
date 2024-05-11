<?php
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Barrio;
use App\Models\Ciudad;
use App\Models\Departamento;
use App\Models\Pais;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class BarriosTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todos_los_barrios_autenticado()
    {
        // Crear una ciudad en la base de datos
        $ciudad = Ciudad::create([
            'nombre' => 'Ciudad Ejemplo',
            'nombre_corto' => 'CE',
            'indicador' => '+99',
            'codigo_postal' => '12345',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);

        // Crear varios barrios asociados a la ciudad
        $barrios = Barrio::factory()->count(3)->create(['ciudad_id' => $ciudad->id]);

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /barrios autenticada
        $response = $this->getJson('/api/barrios');

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_barrios_sin_autenticar()
    {
        // Simula una solicitud GET a /barrios sin autenticación
        $response = $this->getJson('/api/barrios');

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);
    }

    public function test_puede_crear_barrio_autenticado()
    {
        // Crear una ciudad en la base de datos
        $ciudad = Ciudad::create([
            'nombre' => 'Ciudad Ejemplo',
            'nombre_corto' => 'CE',
            'indicador' => '+99',
            'codigo_postal' => '12345',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud POST a /barrios autenticada
        $response = $this->postJson('/api/barrios', [
            'nombre' => 'Nuevo Barrio',
            'nombre_corto' => 'NB',
            'codigo_postal' => '54321',
            'estado' => 1,
            'ciudad_id' => $ciudad->id,
        ]);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el barrio se haya creado en la base de datos
        $this->assertDatabaseHas('barrios', [
            'nombre' => 'Nuevo Barrio',
            'nombre_corto' => 'NB',
            'codigo_postal' => '54321',
            'estado' => 1,
            'ciudad_id' => $ciudad->id,
        ]);
    }

    public function test_no_puede_crear_barrio_sin_autenticar()
    {
        // Crear una ciudad en la base de datos
        $ciudad = Ciudad::create([
            'nombre' => 'Ciudad Ejemplo',
            'nombre_corto' => 'CE',
            'indicador' => '+99',
            'codigo_postal' => '12345',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);

        // Simula una solicitud POST a /barrios sin autenticación
        $response = $this->postJson('/api/barrios', [
            'nombre' => 'Nuevo Barrio',
            'nombre_corto' => 'NB',
            'codigo_postal' => '54321',
            'estado' => 1,
            'ciudad_id' => $ciudad->id,
        ]);

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);

        // Verifica que el barrio no se haya creado en la base de datos
        $this->assertDatabaseMissing('barrios', [
            'nombre' => 'Nuevo Barrio',
            'nombre_corto' => 'NB',
            'codigo_postal' => '54321',
            'estado' => 1,
            'ciudad_id' => $ciudad->id,
        ]);
    }

    public function test_puede_mostrar_un_barrio_existente()
    {
        // Crear una ciudad en la base de datos
        $ciudad = Ciudad::create([
            'nombre' => 'Ciudad Ejemplo',
            'nombre_corto' => 'CE',
            'indicador' => '+99',
            'codigo_postal' => '12345',
            'estado' => 1,
            'departamento_id' => 1, // Reemplaza 1 con el ID del departamento existente
        ]);

        // Crear un barrio asociado a la ciudad
        $barrio = Barrio::create([
            'nombre' => 'Barrio Ejemplo',
            'nombre_corto' => 'BE',
            'codigo_postal' => '54321',
            'estado' => 1,
            'ciudad_id' => $ciudad->id,
        ]);

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /barrios/{id} para mostrar un barrio específico
        $response = $this->getJson('/api/barrios/' . $barrio->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que los datos del barrio devueltos coincidan con el barrio creado
        $response->assertJson([
            'barrio' => $barrio->toArray()
        ]);
    }

    public function test_no_puede_mostrar_un_barrio_inexistente()
    {
        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /barrios/{id} para mostrar un barrio que no existe
        $response = $this->getJson('/api/barrios/999');

                // Verifica que se devuelva un código de estado 404 (No encontrado)
                $response->assertStatus(404);
            }
        
            public function test_puede_actualizar_un_barrio_existente()
            {
                // Crear un barrio en la base de datos
                $barrio = Barrio::create([
                    'nombre' => 'Barrio Ejemplo',
                    'nombre_corto' => 'BE',
                    'codigo_postal' => '54321',
                    'estado' => 1,
                    'ciudad_id' => 1, // Reemplaza 1 con el ID de la ciudad existente
                ]);
        
                // Autenticar al usuario
                Sanctum::actingAs($this->user);
                
                // Simula una solicitud PUT a /barrios/{id} para actualizar un barrio existente
                $response = $this->putJson('/api/barrios/' . $barrio->id, [
                    'nombre' => 'Nuevo Nombre de Barrio',
                ]);
                
                // Verifica que se devuelva una respuesta exitosa (código de estado 200)
                $response->assertStatus(200);
                
                // Verifica que el barrio se haya actualizado correctamente en la base de datos
                $this->assertDatabaseHas('barrios', [
                    'id' => $barrio->id,
                    'nombre' => 'Nuevo Nombre de Barrio',
                ]);
            }
        
            public function test_puede_eliminar_un_barrio_existente()
            {
                // Crear un barrio en la base de datos
                $barrio = Barrio::create([
                    'nombre' => 'Barrio Ejemplo',
                    'nombre_corto' => 'BE',
                    'codigo_postal' => '54321',
                    'estado' => 1,
                    'ciudad_id' => 1, // Reemplaza 1 con el ID de la ciudad existente
                ]);
        
                // Autenticar al usuario
                Sanctum::actingAs($this->user);
        
                // Simula una solicitud DELETE a /barrios/{id} para eliminar un barrio existente
                $response = $this->deleteJson('/api/barrios/' . $barrio->id);
        
                // Verifica que se devuelva una respuesta exitosa (código de estado 200)
                $response->assertStatus(200);
        
                // Verifica que el barrio ya no exista en la base de datos
                $this->assertDatabaseMissing('barrios', [
                    'id' => $barrio->id
                ]);
            }
        }
        
