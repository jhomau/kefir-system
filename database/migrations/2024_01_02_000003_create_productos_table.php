<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_producto')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('unidad_medida', ['unidad', 'litro', 'botella'])->default('unidad');
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('precio_costo', 10, 2)->nullable();
            $table->decimal('stock_minimo', 10, 3)->default(0);
            $table->boolean('activo')->default(true);
            $table->boolean('vendible_online')->default(false);
            $table->string('imagen')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
