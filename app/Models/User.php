<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'correo',
        'contrasena',
        'telefono',
        'activo',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->activo && $this->hasAnyRole(['administrador', 'vendedor']);
    }

    public function getFilamentName(): string
    {
        return $this->nombre;
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'usuario_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'usuario_id');
    }

    protected function casts(): array
    {
        return [
            'correo_verificado_en' => 'datetime',
            'contrasena' => 'hashed',
            'activo' => 'boolean',
        ];
    }
}
