<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'numero_venta',
        'cliente_id',
        'usuario_id',
        'tipo_venta',
        'canal',
        'estado',
        'subtotal',
        'descuento',
        'total',
        'monto_pagado',
        'estado_pago',
        'reservado_hasta',
        'fecha_venta',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'descuento' => 'decimal:2',
            'total' => 'decimal:2',
            'monto_pagado' => 'decimal:2',
            'reservado_hasta' => 'datetime',
            'fecha_venta' => 'datetime',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'venta_id');
    }
}
