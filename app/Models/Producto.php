<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'codigo_producto',
        'nombre',
        'descripcion',
        'unidad_medida',
        'precio_venta',
        'precio_costo',
        'stock_minimo',
        'activo',
        'vendible_online',
        'imagen',
    ];

    protected function casts(): array
    {
        return [
            'precio_venta' => 'decimal:2',
            'precio_costo' => 'decimal:2',
            'stock_minimo' => 'decimal:3',
            'activo' => 'boolean',
            'vendible_online' => 'boolean',
        ];
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }

    public function lotes()
    {
        return $this->hasMany(LoteProducto::class, 'producto_id');
    }

    public function stockDisponible(): float
    {
        return (float) $this->lotes()->sum('cantidad_disponible');
    }
}
