<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('codigo')->nullable()->unique();
            $table->string('nombre');
            $table->string('correo')->nullable();
            $table->string('telefono');
            $table->text('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->enum('tipo_cliente', ['persona', 'negocio'])->default('persona');
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('telefono');
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
