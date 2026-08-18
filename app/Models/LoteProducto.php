<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteProducto extends Model
{
    protected $table = 'lotes_producto';

    protected $fillable = [
        'producto_id',
        'codigo_lote',
        'fecha_produccion',
        'fecha_vencimiento',
        'cantidad_producida',
        'cantidad_disponible',
        'cantidad_reservada',
        'costo_produccion',
        'notas',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha_produccion' => 'date',
            'fecha_vencimiento' => 'date',
            'cantidad_producida' => 'decimal:3',
            'cantidad_disponible' => 'decimal:3',
            'cantidad_reservada' => 'decimal:3',
            'costo_produccion' => 'decimal:2',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
