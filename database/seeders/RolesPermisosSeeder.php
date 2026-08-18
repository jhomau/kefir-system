<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            ['name' => 'usuarios.ver', 'nombre_visible' => 'Ver usuarios', 'modulo' => 'usuarios'],
            ['name' => 'usuarios.crear', 'nombre_visible' => 'Crear usuarios', 'modulo' => 'usuarios'],
            ['name' => 'usuarios.editar', 'nombre_visible' => 'Editar usuarios', 'modulo' => 'usuarios'],
            ['name' => 'usuarios.eliminar', 'nombre_visible' => 'Eliminar usuarios', 'modulo' => 'usuarios'],
            ['name' => 'roles.ver', 'nombre_visible' => 'Ver roles', 'modulo' => 'roles'],
            ['name' => 'roles.crear', 'nombre_visible' => 'Crear roles', 'modulo' => 'roles'],
            ['name' => 'roles.editar', 'nombre_visible' => 'Editar roles', 'modulo' => 'roles'],
            ['name' => 'roles.eliminar', 'nombre_visible' => 'Eliminar roles', 'modulo' => 'roles'],
            ['name' => 'clientes.ver', 'nombre_visible' => 'Ver clientes', 'modulo' => 'clientes'],
            ['name' => 'clientes.crear', 'nombre_visible' => 'Crear clientes', 'modulo' => 'clientes'],
            ['name' => 'clientes.editar', 'nombre_visible' => 'Editar clientes', 'modulo' => 'clientes'],
            ['name' => 'clientes.eliminar', 'nombre_visible' => 'Eliminar clientes', 'modulo' => 'clientes'],
            ['name' => 'productos.ver', 'nombre_visible' => 'Ver productos', 'modulo' => 'productos'],
            ['name' => 'productos.crear', 'nombre_visible' => 'Crear productos', 'modulo' => 'productos'],
            ['name' => 'productos.editar', 'nombre_visible' => 'Editar productos', 'modulo' => 'productos'],
            ['name' => 'productos.eliminar', 'nombre_visible' => 'Eliminar productos', 'modulo' => 'productos'],
            ['name' => 'inventario.ver', 'nombre_visible' => 'Ver inventario', 'modulo' => 'inventario'],
            ['name' => 'inventario.registrar_produccion', 'nombre_visible' => 'Registrar producción', 'modulo' => 'inventario'],
            ['name' => 'inventario.ajustar', 'nombre_visible' => 'Ajustar stock', 'modulo' => 'inventario'],
            ['name' => 'inventario.merma', 'nombre_visible' => 'Registrar merma', 'modulo' => 'inventario'],
            ['name' => 'ventas.ver', 'nombre_visible' => 'Ver ventas', 'modulo' => 'ventas'],
            ['name' => 'ventas.crear', 'nombre_visible' => 'Crear ventas', 'modulo' => 'ventas'],
            ['name' => 'ventas.anular', 'nombre_visible' => 'Anular ventas', 'modulo' => 'ventas'],
            ['name' => 'pagos.registrar', 'nombre_visible' => 'Registrar pagos', 'modulo' => 'pagos'],
            ['name' => 'pagos.ver', 'nombre_visible' => 'Ver pagos', 'modulo' => 'pagos'],
            ['name' => 'gastos.ver', 'nombre_visible' => 'Ver gastos', 'modulo' => 'gastos'],
            ['name' => 'gastos.crear', 'nombre_visible' => 'Registrar gastos', 'modulo' => 'gastos'],
            ['name' => 'reportes.ver', 'nombre_visible' => 'Ver reportes', 'modulo' => 'reportes'],
            ['name' => 'reportes.exportar', 'nombre_visible' => 'Exportar reportes', 'modulo' => 'reportes'],
            ['name' => 'reservas.ver', 'nombre_visible' => 'Ver reservas', 'modulo' => 'reservas'],
            ['name' => 'reservas.crear', 'nombre_visible' => 'Crear reservas', 'modulo' => 'reservas'],
            ['name' => 'tienda.ver_catalogo', 'nombre_visible' => 'Ver catálogo web', 'modulo' => 'tienda'],
            ['name' => 'tienda.realizar_pedido', 'nombre_visible' => 'Realizar pedido', 'modulo' => 'tienda'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::query()->firstOrCreate(
                ['name' => $permiso['name'], 'guard_name' => 'web'],
                array_merge($permiso, ['guard_name' => 'web', 'activo' => true])
            );
        }

        $admin = Rol::query()->firstOrCreate(
            ['name' => 'administrador', 'guard_name' => 'web'],
            [
                'nombre_visible' => 'Administrador',
                'descripcion' => 'Acceso total al sistema',
                'es_sistema' => true,
                'activo' => true,
            ]
        );
        $admin->syncPermissions(Permiso::all());

        $vendedor = Rol::query()->firstOrCreate(
            ['name' => 'vendedor', 'guard_name' => 'web'],
            [
                'nombre_visible' => 'Vendedor',
                'descripcion' => 'Registra ventas y clientes',
                'es_sistema' => true,
                'activo' => true,
            ]
        );
        $vendedor->syncPermissions([
            'clientes.ver', 'clientes.crear', 'clientes.editar',
            'productos.ver',
            'inventario.ver',
            'ventas.ver', 'ventas.crear',
            'pagos.registrar', 'pagos.ver',
            'reportes.ver',
        ]);

        $cliente = Rol::query()->firstOrCreate(
            ['name' => 'cliente', 'guard_name' => 'web'],
            [
                'nombre_visible' => 'Cliente',
                'descripcion' => 'Acceso a tienda y reservas web',
                'es_sistema' => true,
                'activo' => true,
            ]
        );
        $cliente->syncPermissions([
            'tienda.ver_catalogo',
            'tienda.realizar_pedido',
            'reservas.ver',
            'reservas.crear',
        ]);

        $usuarioAdmin = User::query()->firstOrCreate(
            ['correo' => 'admin@kefir.local'],
            [
                'nombre' => 'Administrador',
                'contrasena' => Hash::make('password'),
                'telefono' => null,
                'activo' => true,
            ]
        );
        $usuarioAdmin->assignRole($admin);
    }
}
