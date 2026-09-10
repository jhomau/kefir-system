<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = [
        'nombre_negocio',
        'eslogan',
        'telefono',
        'whatsapp',
        'correo',
        'direccion',
        'horario',
        'mensaje_tienda',
    ];

    public static function defaults(): array
    {
        return [
            'nombre_negocio' => 'Kefir System',
            'eslogan' => 'Kefir de leche natural',
            'telefono' => null,
            'whatsapp' => null,
            'correo' => null,
            'direccion' => null,
            'horario' => null,
            'mensaje_tienda' => 'Productos disponibles para pedido web.',
        ];
    }

    public static function disponible(): bool
    {
        try {
            return Schema::hasTable((new static)->getTable());
        } catch (\Throwable) {
            return false;
        }
    }

    public static function actual(): self
    {
        $registro = static::query()->first();

        if ($registro) {
            return $registro;
        }

        return static::query()->create(static::defaults());
    }

    public static function fallback(): self
    {
        return new static(static::defaults());
    }

    public static function nombreNegocio(): string
    {
        if (! static::disponible()) {
            return 'Kefir System';
        }

        return static::actual()->nombre_negocio ?: 'Kefir System';
    }

    public function enlaceWhatsapp(): ?string
    {
        if (blank($this->whatsapp)) {
            return null;
        }

        $numero = preg_replace('/\D+/', '', $this->whatsapp);

        return $numero ? 'https://wa.me/'.$numero : null;
    }
}
