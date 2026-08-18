<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_gasto', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_gasto_id')->constrained('categorias_gasto')->restrictOnDelete();
            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_gasto');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'qr']);
            $table->string('nombre_proveedor')->nullable();
            $table->string('referencia')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->timestamps();

            $table->index('fecha_gasto');
        });

        Schema::create('otros_ingresos', function (Blueprint $table) {
            $table->id();
            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_ingreso');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'qr']);
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->timestamps();

            $table->index('fecha_ingreso');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otros_ingresos');
        Schema::dropIfExists('gastos');
        Schema::dropIfExists('categorias_gasto');
    }
};
