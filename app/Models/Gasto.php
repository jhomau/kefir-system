<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';

    protected $fillable = [
        'categoria_gasto_id',
        'concepto',
        'monto',
        'fecha_gasto',
        'metodo_pago',
        'nombre_proveedor',
        'referencia',
        'notas',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_gasto' => 'date',
        ];
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaGasto::class, 'categoria_gasto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
