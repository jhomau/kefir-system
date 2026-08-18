<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtroIngreso extends Model
{
    protected $table = 'otros_ingresos';

    protected $fillable = [
        'concepto',
        'monto',
        'fecha_ingreso',
        'metodo_pago',
        'notas',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_ingreso' => 'date',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
