<?php

namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Menu;
use App\Models\Permisos;
use Laravel\Sanctum\Sanctum;

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
        Sanctum::actingAs($this->user);
        $response = $this
                              ->get('/api/menu');

        $response->assertStatus(200);
    }

    public function test_se_puede_obtener_menus(): void
    {
        Sanctum::actingAs($this->user);
        $response = $this
                                    ->get('/api/menu');

        $response->assertJsonFragment([
            'data' => []
        ]);
    }



    public function test_se_puede_crear_menu(): void
    {
        // Crear un permiso temporal
        $permiso = Permisos::create([
            'name' => 'Permiso temporal', // Proporciona un nombre válido para el permiso
            'guard_name' => 'web', // Proporciona un guard_name válido
            // Otros campos necesarios aquí
        ]);
    
        // Verificar que se haya creado el permiso
        $this->assertNotNull($permiso);
    
        // Autenticar al usuario (si es necesario)
        Sanctum::actingAs($this->user);
    
        // Enviar una solicitud POST JSON para crear un menú asociado al permiso creado
        $response = $this->postJson('/api/menu', [
            'nombre' => 'menu_prueba',
            'id_referencia' => 'ref_prueba',
            'icono' => 'icono_prueba',
            'color' => 'color_prueba',
            'tipo_icono' => 'imagen',
            'estado' => true,
            'permiso_id' => $permiso->id, // Utiliza el ID del permiso creado
        ]);
    
        // Verificar que la solicitud haya sido exitosa con el código de estado 201
        $response->assertStatus(200);
    }
    




    public function test_se_puede_actualizar_menu(): void
    {
        // Crear un permiso temporal
        $permiso = Permisos::create([
            'name' => 'Permiso temporal', // Proporciona un nombre válido para el permiso
            'guard_name' => 'web', // Proporciona un guard_name válido
            // Otros campos necesarios aquí
        ]);

        // Verificar que se haya creado el permiso
        $this->assertNotNull($permiso);

        // Crear un menú en la base de datos con el permiso temporal
        $menu = Menu::create([
            'nombre' => 'Nombre Inicial',
            'id_referencia' => 1, // Proporciona un valor válido para id_referencia
            'permiso_id' => $permiso->id, // Utiliza el ID del permiso creado
            // Añade los demás campos necesarios aquí
        ]);

        // Autenticar al usuario (si es necesario)
        Sanctum::actingAs($this->user);

        // Enviar una solicitud PUT JSON para actualizar el menú
        $response = $this->putJson("/api/menu/{$menu->id}", [
            'nombre' => 'Nombre Actualizado',
            // Proporciona otros campos que deseas actualizar
        ]);

        // Verificar que la solicitud sea exitosa
        $response->assertStatus(200);

        // Verificar que el menú haya sido actualizado correctamente en la base de datos
        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'nombre' => 'Nombre Actualizado',
            // Añade otros campos actualizados aquí
        ]);
    }






    public function test_se_puede_eliminar_menu(): void
    {
        // Crear un permiso temporalmente
        $permiso = Permisos::create([
            'name' => 'Permiso temporal', // Asegúrate de proporcionar un nombre válido
            'guard_name' => 'web', // Asegúrate de proporcionar un valor válido para guard_name
            // Otros campos necesarios aquí
        ]);

        // Verificar que se haya creado el permiso
        $this->assertNotNull($permiso);

        // Crear un menú en la base de datos con el permiso temporal
        $menu = Menu::create([
            'nombre' => 'Nombre del menú',
            'id_referencia' => 1, // Asigna un valor válido para id_referencia
            'permiso_id' => $permiso->id, // Asigna el id del permiso temporal
            // Añade los demás campos necesarios aquí
        ]);

        // Autenticar al usuario (si es necesario)
        Sanctum::actingAs($this->user);

        // Enviar una solicitud DELETE para eliminar el menú
        $response = $this->delete("/api/menu/{$menu->id}");

        // Verificar que la solicitud sea exitosa
        $response->assertStatus(200); // 204 No Content

        // Verificar que el menú haya sido eliminado de la base de datos
        // $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }


}
