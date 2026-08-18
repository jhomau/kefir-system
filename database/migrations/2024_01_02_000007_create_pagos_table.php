<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'qr']);
            $table->dateTime('fecha_pago');
            $table->string('referencia')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->timestamps();

            $table->index('fecha_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
