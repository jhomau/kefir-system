<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('codigo_lote')->unique();
            $table->date('fecha_produccion');
            $table->date('fecha_vencimiento');
            $table->decimal('cantidad_producida', 10, 3);
            $table->decimal('cantidad_disponible', 10, 3);
            $table->decimal('cantidad_reservada', 10, 3)->default(0);
            $table->decimal('costo_produccion', 10, 2)->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('registrado_por')->constrained('usuarios')->restrictOnDelete();
            $table->timestamps();

            $table->index('fecha_vencimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes_producto');
    }
};
