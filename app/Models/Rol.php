<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Rol extends SpatieRole
{
    protected $fillable = [
        'name',
        'nombre_visible',
        'descripcion',
        'guard_name',
        'es_sistema',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'es_sistema' => 'boolean',
            'activo' => 'boolean',
        ];
    }
}
