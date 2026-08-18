<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'usuario_id',
        'codigo',
        'nombre',
        'correo',
        'telefono',
        'direccion',
        'ciudad',
        'tipo_cliente',
        'notas',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }
}
