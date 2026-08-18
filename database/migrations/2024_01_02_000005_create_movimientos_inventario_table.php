<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->foreignId('lote_producto_id')->nullable()->constrained('lotes_producto')->nullOnDelete();
            $table->enum('tipo_movimiento', ['produccion', 'venta', 'ajuste', 'merma', 'devolucion']);
            $table->decimal('cantidad', 10, 3);
            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->string('referencia_tipo')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->dateTime('fecha_movimiento');
            $table->timestamps();

            $table->index(['referencia_tipo', 'referencia_id']);
            $table->index('fecha_movimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
