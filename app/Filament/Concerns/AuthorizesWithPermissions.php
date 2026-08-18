<?php

namespace App\Filament\Concerns;

trait AuthorizesWithPermissions
{
    abstract protected static function permissionPrefix(): string;

    protected static function userCan(string $action): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole('administrador')) {
            return true;
        }

        return $user->can(static::permissionPrefix().'.'.$action);
    }

    public static function canViewAny(): bool
    {
        return static::userCan('ver');
    }

    public static function canCreate(): bool
    {
        return static::userCan('crear') || static::userCan('registrar_produccion') || static::userCan('registrar');
    }

    public static function canEdit($record): bool
    {
        return static::userCan('editar') || static::userCan('ajustar');
    }

    public static function canDelete($record): bool
    {
        return static::userCan('eliminar') || static::userCan('anular');
    }
}
