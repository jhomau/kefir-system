<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta')->unique();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->enum('tipo_venta', ['venta', 'reserva', 'pedido_web'])->default('venta');
            $table->enum('canal', ['mostrador', 'telefono', 'web', 'whatsapp'])->default('mostrador');
            $table->enum('estado', [
                'borrador',
                'completada',
                'reservada',
                'confirmada',
                'entregada',
                'cancelada',
            ])->default('borrador');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->enum('estado_pago', ['pendiente', 'parcial', 'pagado'])->default('pendiente');
            $table->dateTime('reservado_hasta')->nullable();
            $table->dateTime('fecha_venta');
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fecha_venta');
            $table->index('estado');
        });

        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->foreignId('lote_producto_id')->nullable()->constrained('lotes_producto')->nullOnDelete();
            $table->decimal('cantidad', 10, 3);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
        Schema::dropIfExists('ventas');
    }
};
