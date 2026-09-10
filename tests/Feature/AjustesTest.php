<?php

namespace Tests\Feature;

use App\Filament\Pages\Ajustes;
use App\Models\Configuracion;
use App\Models\User;
use Database\Seeders\RolesPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AjustesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesPermisosSeeder::class);
    }

    public function test_storefront_shows_saved_business_settings(): void
    {
        Configuracion::actual()->update([
            'nombre_negocio' => 'Lácteos Andes',
            'eslogan' => 'Kefir artesanal',
            'mensaje_tienda' => 'Pedidos hasta las 18:00',
            'telefono' => '77711122',
            'whatsapp' => '59177711122',
            'correo' => 'hola@andes.test',
            'direccion' => 'Av. Principal 100',
            'horario' => 'Lun-Vie 8:00-18:00',
        ]);

        $this->get('/tienda')
            ->assertOk()
            ->assertSee('Lácteos Andes', false)
            ->assertSee('Kefir artesanal', false)
            ->assertSee('Pedidos hasta las 18:00', false)
            ->assertSee('77711122', false)
            ->assertSee('59177711122', false)
            ->assertSee('hola@andes.test', false)
            ->assertSee('Av. Principal 100', false)
            ->assertSee('Lun-Vie 8:00-18:00', false);
    }

    public function test_whatsapp_link_keeps_only_digits(): void
    {
        $config = new Configuracion(['whatsapp' => '+591 777-11-122']);

        $this->assertSame('https://wa.me/59177711122', $config->enlaceWhatsapp());
    }

    public function test_admin_can_save_settings_from_filament_page(): void
    {
        $admin = User::query()->where('correo', 'admin@kefir.local')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(Ajustes::class)
            ->fillForm([
                'nombre_negocio' => 'Kefir del Valle',
                'eslogan' => 'Fresco cada día',
                'telefono' => '70000000',
                'whatsapp' => '59170000000',
                'correo' => 'contacto@valle.test',
                'direccion' => 'Calle 1',
                'horario' => 'Todos los días',
                'mensaje_tienda' => 'Envíos a domicilio',
            ])
            ->call('guardar')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas('configuraciones', [
            'nombre_negocio' => 'Kefir del Valle',
            'eslogan' => 'Fresco cada día',
            'mensaje_tienda' => 'Envíos a domicilio',
            'correo' => 'contacto@valle.test',
        ]);
    }

    public function test_vendedor_cannot_open_ajustes(): void
    {
        $vendedor = User::factory()->create();
        $vendedor->assignRole('vendedor');

        $this->actingAs($vendedor)
            ->get('/admin/ajustes')
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_ajustes(): void
    {
        $this->get('/admin/ajustes')->assertRedirect();
    }
}
