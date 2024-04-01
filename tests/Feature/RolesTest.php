<?php
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Permisos;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

    class RolesControllerTest extends TestCase
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
            $response = $this->actingAs($this->user)
                            ->getJson('/api/roles');

            // Verifica que se devuelva una respuesta exitosa (código de estado 200)
            $response->assertStatus(200);

            // Verifica que los datos de los roles estén en el formato correcto
            $response->assertJsonStructure([
                'roles' => [
                    '*' => ['id', 'name', 'guard_name'],
                ],
            ]);
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
            // Simula una solicitud POST a /roles autenticada
            $response = $this->actingAs($this->user)
                            ->postJson('/api/roles', [
                                'name' => 'Nuevo Rol',
                            ]);

            // Verifica que se devuelva una respuesta exitosa (código de estado 201)
            $response->assertStatus(201);

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
            // Crea un rol en la base de datos
            $rol = Roles::factory()->create();

            // Simula una solicitud GET a /roles/{id} para mostrar un rol específico
            $response = $this->actingAs($this->user)
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
            // Simula una solicitud GET a /roles/{id} para mostrar un rol que no existe
            $response = $this->actingAs($this->user)
                            ->getJson('/api/roles/999');

            // Verifica que se devuelva un código de estado 404 (No encontrado)
            $response->assertStatus(404);
        }

    public function test_puede_actualizar_un_rol_existente()
    {
        // Crea un rol en la base de datos
        $rol = Roles::factory()->create();

        // Simula una solicitud PUT a /roles/{id} para actualizar un rol existente
        $response = $this->actingAs($this->user)
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
        // Crea un rol en la base de datos
        $rol = Roles::factory()->create();

        // Simula una solicitud DELETE a /roles/{id} para eliminar un rol existente
        $response = $this->actingAs($this->user)
                        ->deleteJson('/api/roles/' . $rol->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que el rol ya no exista en la base de datos
        $this->assertDatabaseMissing('roles', ['id' => $rol->id]);
    }

}
