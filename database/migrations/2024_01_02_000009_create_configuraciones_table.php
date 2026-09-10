<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_negocio')->default('Kefir System');
            $table->string('eslogan')->nullable();
            $table->string('telefono')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('correo')->nullable();
            $table->string('direccion')->nullable();
            $table->string('horario')->nullable();
            $table->text('mensaje_tienda')->nullable();
            $table->timestamps();
        });

        DB::table('configuraciones')->insert([
            'nombre_negocio' => 'Kefir System',
            'eslogan' => 'Kefir de leche natural',
            'mensaje_tienda' => 'Productos disponibles para pedido web.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (Schema::hasTable('permisos') && ! DB::table('permisos')->where('name', 'configuracion.editar')->exists()) {
            $permisoId = DB::table('permisos')->insertGetId([
                'name' => 'configuracion.editar',
                'nombre_visible' => 'Editar ajustes',
                'modulo' => 'configuracion',
                'guard_name' => 'web',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $admin = DB::table('roles')->where('name', 'administrador')->first();

            if ($admin) {
                DB::table('rol_permisos')->insert([
                    'permission_id' => $permisoId,
                    'role_id' => $admin->id,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permisos')) {
            $permiso = DB::table('permisos')->where('name', 'configuracion.editar')->first();

            if ($permiso) {
                DB::table('rol_permisos')->where('permission_id', $permiso->id)->delete();
                DB::table('usuario_permisos')->where('permission_id', $permiso->id)->delete();
                DB::table('permisos')->where('id', $permiso->id)->delete();
            }
        }

        Schema::dropIfExists('configuraciones');
    }
};
