<?php
use App\Models\Galeria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Testing\TestResponse;

class GaleriasCultivosTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->user = User::factory()->create();
    }

    // Pruebas de obtención de galerías

    public function test_puede_obtener_todas_las_galerias_de_un_cultivo_autenticado()
    {
        Sanctum::actingAs($this->user);
        
        $response = $this->getJson('/api/galerias-cultivos?cultivo_id=1');

        $response->assertStatus(200);
    }

    public function test_no_puede_obtener_galerias_de_un_cultivo_sin_autenticar()
    {
        $response = $this->getJson('/api/galerias-cultivos?cultivo_id=1');

        $response->assertStatus(401);
    }

    // Pruebas de creación de galerías

    public function test_puede_crear_una_galeria_autenticado_con_un_archivo()
    {
        Sanctum::actingAs($this->user);

        Storage::fake('public');

        $file = UploadedFile::fake()->image('galeria1.jpg');

        $response = $this->postJson('/api/galerias-cultivos', [
            'cultivo_id' => 1,
            'galeria' => $file,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'galeria' => [
                'id',
                'nombre',
                'url',
                'tipo',
                'cultivo_id',
                'created_at',
                'updated_at',
            ],
            'mensaje'
        ]);

        $this->assertTrue(Storage::disk('public')->exists('cultivos/galerias/' . $file->hashName()));

    }

    public function test_puede_crear_una_galeria_autenticado_con_varios_archivos()
    {
        Sanctum::actingAs($this->user);

        Storage::fake('public');

        $files = [
            UploadedFile::fake()->image('galeria1.jpg'),
            UploadedFile::fake()->image('galeria2.jpg'),
            UploadedFile::fake()->image('galeria3.jpg'),
        ];

        $response = $this->postJson('/api/galerias-cultivos', [
            'cultivo_id' => 1,
            'galerias' => $files,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'galeria' => [
                '*' => [
                    'id',
                    'nombre',
                    'url',
                    'tipo',
                    'cultivo_id',
                    'created_at',
                    'updated_at',
                ]
            ],
            'mensaje'
        ]);

        foreach ($files as $file) {
            $this->assertTrue(Storage::disk('public')->exists('cultivos/galerias/' . $file->hashName()));

        }
    }

    // Pruebas de eliminación de galerías

    public function test_puede_eliminar_una_galeria_existente()
    {
        Sanctum::actingAs($this->user);

        $galeria = Galeria::create([
            'nombre' => 'galeria1.jpg',
            'url' => 'http://example.com/galeria1.jpg',
            'tipo' => 'imagen',
            'cultivo_id' => 1,
        ]);

        $response = $this->deleteJson('/api/galerias-cultivos/' . $galeria->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('galerias', ['id' => $galeria->id]);
    }

    public function test_no_puede_eliminar_una_galeria_inexistente()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson('/api/galerias-cultivos/999');

        $response->assertStatus(404);
    }
}
