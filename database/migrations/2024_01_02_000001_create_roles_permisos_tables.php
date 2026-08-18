<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nombre_visible');
            $table->string('modulo');
            $table->text('descripcion')->nullable();
            $table->string('guard_name')->default('web');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nombre_visible');
            $table->text('descripcion')->nullable();
            $table->string('guard_name')->default('web');
            $table->boolean('es_sistema')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permisos')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();

            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('usuario_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type']);
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('usuario_permisos', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permisos')->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type']);
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_permisos');
        Schema::dropIfExists('usuario_roles');
        Schema::dropIfExists('rol_permisos');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permisos');
    }
};
