<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permiso extends SpatiePermission
{
    protected $table = 'permisos';

    protected $fillable = [
        'name',
        'nombre_visible',
        'modulo',
        'descripcion',
        'guard_name',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }
}
