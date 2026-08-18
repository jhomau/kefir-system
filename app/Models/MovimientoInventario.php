<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id',
        'lote_producto_id',
        'tipo_movimiento',
        'cantidad',
        'costo_unitario',
        'referencia_tipo',
        'referencia_id',
        'notas',
        'usuario_id',
        'fecha_movimiento',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:3',
            'costo_unitario' => 'decimal:2',
            'fecha_movimiento' => 'datetime',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function lote()
    {
        return $this->belongsTo(LoteProducto::class, 'lote_producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
