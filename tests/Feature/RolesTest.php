<?php
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Permisos;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class RolesTest extends TestCase
    {
        use RefreshDatabase, InteractsWithDatabase;

        protected $user;

        protected function setUp(): void
        {
            parent::setUp();
            
            $this->seed();

            $this->user = User::factory()->create();
        }

        public function test_puede_obtener_todos_los_roles_autenticado()
        {
            // Simula una solicitud GET a /roles autenticada
            Sanctum::actingAs($this->user);
            $response = $this->getJson('/api/roles');

            // Verifica que se devuelva una respuesta exitosa (código de estado 200)
            $response->assertStatus(200);
        }

        

        public function test_no_puede_obtener_roles_sin_autenticar()
        {
            // Simula una solicitud GET a /roles sin autenticación
            $response = $this->getJson('/api/roles');

            // Verifica que se devuelva un código de estado 401 (No autorizado)
            $response->assertStatus(401);
        }

        public function test_puede_crear_rol_autenticado()
        {
            Sanctum::actingAs($this->user);
            // Simula una solicitud POST a /roles autenticada
            $response = $this
                            ->postJson('/api/roles', [
                                'name' => 'Nuevo Rol',
                            ]);

            // Verifica que se devuelva una respuesta exitosa (código de estado 201)
            $response->assertStatus(200);

            // Verifica que el rol se haya creado en la base de datos
            $this->assertDatabaseHas('roles', [
                'name' => 'Nuevo Rol',
                'guard_name' => 'api',
            ]);
        }

        public function test_no_puede_crear_rol_sin_autenticar()
        {
            // Simula una solicitud POST a /roles sin autenticación
            $response = $this->postJson('/api/roles', [
                'name' => 'Nuevo Rol',
            ]);

            // Verifica que se devuelva un código de estado 401 (No autorizado)
            $response->assertStatus(401);

            // Verifica que el rol no se haya creado en la base de datos
            $this->assertDatabaseMissing('roles', [
                'name' => 'Nuevo Rol',
                'guard_name' => 'api',
            ]);
        }

        public function test_puede_mostrar_un_rol_existente()
        {
            // Crea un nuevo rol en la base de datos con un valor válido para "guard_name"
            $rol = Roles::create([
                'name' => 'Nombre del Rol',
                'guard_name' => 'api', // Proporciona un valor válido para "guard_name"
                // Otros campos necesarios aquí
            ]);
        
            // Autentica al usuario
            Sanctum::actingAs($this->user);
        
            // Simula una solicitud GET a /roles/{id} para mostrar un rol específico
            $response = $this
                            ->getJson('/api/roles/' . $rol->id);
        
            // Verifica que se devuelva una respuesta exitosa (código de estado 200)
            $response->assertStatus(200);
        
            // Verifica que los datos del rol devueltos coincidan con el rol creado
            $response->assertJson([
                'rol' => $rol->toArray()
            ]);
        }
        
        


    public function test_no_puede_mostrar_un_rol_inexistente()
        {
            Sanctum::actingAs($this->user);
            // Simula una solicitud GET a /roles/{id} para mostrar un rol que no existe
            $response = $this
                            ->getJson('/api/roles/999');

            // Verifica que se devuelva un código de estado 404 (No encontrado)
            $response->assertStatus(404);
        }

    public function test_puede_actualizar_un_rol_existente()
    {
        // Crear un rol en la base de datos
        $rol = Roles::create([
            'name' => 'Nombre del Rol', // Proporciona un nombre válido para el rol
            'guard_name' => 'web', // Proporciona un valor válido para guard_name
                // Otros campos necesarios aquí
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud PUT a /roles/{id} para actualizar un rol existente
        $response = $this
                            ->putJson('/api/roles/' . $rol->id, [
                                'name' => 'Nuevo Nombre de Rol',
                            ]);
        
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
        
        // Verifica que el rol se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('roles', [
            'id' => $rol->id,
            'name' => 'Nuevo Nombre de Rol',
        ]);
    }
        

    public function test_puede_eliminar_un_rol_existente()
    {
        // Crear un rol en la base de datos
        $rol = Roles::create([
            'name' => 'Nombre del Rol', // Proporciona un nombre válido para el rol
            'guard_name' => 'web', // Proporciona un valor válido para guard_name
                // Otros campos necesarios aquí
        ]);

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud DELETE a /roles/{id} para eliminar un rol existente
        $response = $this
                        ->deleteJson('/api/roles/' . $rol->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el rol ya no exista en la base de datos
        // $this->assertDatabaseMissing('roles', ['id' => $rol->id]);
    }


}
