<?php
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class CategoriasTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    public function test_puede_obtener_todas_las_categorias_autenticado()
    {
        // Simula una solicitud GET a /categorias autenticada
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/categorias');

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_categorias_sin_autenticar()
    {
        // Simula una solicitud GET a /categorias sin autenticación
        $response = $this->getJson('/api/categorias');

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);
    }

    public function test_puede_crear_categoria_autenticado()
    {
        Sanctum::actingAs($this->user);
        // Simula una solicitud POST a /categorias autenticada
        $response = $this->postJson('/api/categorias', [
            'nombre' => 'Nueva Categoría',
            'nombre_corto' => 'NC',
            'estado' => 1
        ]);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que la categoría se haya creado en la base de datos
        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Nueva Categoría',
            'nombre_corto' => 'NC',
            'estado' => 1
        ]);
    }

    public function test_no_puede_crear_categoria_sin_autenticar()
    {
        // Simula una solicitud POST a /categorias sin autenticación
        $response = $this->postJson('/api/categorias', [
            'nombre' => 'Nueva Categoría',
            'nombre_corto' => 'NC',
            'estado' => 1
        ]);

        // Verifica que se devuelva un código de estado 401 (No autorizado)
        $response->assertStatus(401);

        // Verifica que la categoría no se haya creado en la base de datos
        $this->assertDatabaseMissing('categorias', [
            'nombre' => 'Nueva Categoría',
            'nombre_corto' => 'NC',
            'estado' => 1
        ]);
    }

    public function test_puede_mostrar_una_categoria_existente()
    {
        // Crea una nueva categoría en la base de datos
        $categoria = Categoria::create([
            'nombre' => 'Nombre de la Categoría',
            'nombre_corto' => 'NC',
            'estado' => 1
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /categorias/{id} para mostrar una categoría específica
        $response = $this->getJson('/api/categorias/' . $categoria->id);

        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que los datos de la categoría devueltos coincidan con la categoría creada
        $response->assertJson([
            'categoria' => $categoria->toArray()
        ]);
    }

    public function test_no_puede_mostrar_una_categoria_inexistente()
    {
        // Autentica al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud GET a /categorias/{id} para mostrar una categoría que no existe
        $response = $this->getJson('/api/categorias/999');

        // Verifica que se devuelva un código de estado 404 (No encontrado)
        $response->assertStatus(404);
    }

    public function test_puede_actualizar_una_categoria_existente()
    {
        // Crear una categoría en la base de datos
        $categoria = Categoria::create([
            'nombre' => 'Nombre de la Categoría',
            'nombre_corto' => 'NC',
            'estado' => 1
        ]);

        // Autentica al usuario
        Sanctum::actingAs($this->user);
        
        // Simula una solicitud PUT a /categorias/{id} para actualizar una categoría existente
        $response = $this->putJson('/api/categorias/' . $categoria->id, [
            'nombre' => 'Nuevo Nombre de Categoría',
            'nombre_corto' => 'NNC',
            'estado' => 0
        ]);
        
        // Verifica que se devuelva una respuesta exitosa (código de estado 200)
        $response->assertStatus(200);
        
        // Verifica que la categoría se haya actualizado correctamente en la base de datos
        $this->assertDatabaseHas('categorias', [
            'id' => $categoria->id,
            'nombre' => 'Nuevo Nombre de Categoría',
            'nombre_corto' => 'NNC',
            'estado' => 0
        ]);
    }

    public function test_puede_eliminar_una_categoria_existente()
    {
        // Crear una categoría en la base de datos
        $categoria = Categoria::create([
            'nombre' => 'Nombre de la Categoría',
            'nombre_corto' => 'NC',
            'estado' => 1
        ]);

        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Simula una solicitud DELETE a /categorias/{id} para eliminar una categoría existente
        $response = $this->deleteJson('/api/categorias/' . $categoria->id);

        // Verifica que se devuelva una respuesta
        // exitosa (código de estado 200)
        $response->assertStatus(200);

        // Verifica que la categoría ya no exista en la base de datos
        $this->assertDatabaseMissing('categorias', [
            'id' => $categoria->id
        ]);
    }

}
