<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class VentaService
{
    public function __construct(
        protected InventarioService $inventario
    ) {}

    public function generarNumeroVenta(): string
    {
        $prefijo = 'V-'.now()->format('Ymd');
        $ultimo = Venta::query()
            ->where('numero_venta', 'like', $prefijo.'%')
            ->orderByDesc('numero_venta')
            ->value('numero_venta');

        $secuencia = $ultimo ? ((int) substr($ultimo, -4)) + 1 : 1;

        return sprintf('%s-%04d', $prefijo, $secuencia);
    }

    public function crearVenta(array $ventaData, array $lineas, int $usuarioId): Venta
    {
        return DB::transaction(function () use ($ventaData, $lineas, $usuarioId) {
            if (empty($lineas)) {
                throw new InvalidArgumentException('La venta debe tener al menos un producto.');
            }

            $venta = Venta::query()->create([
                'numero_venta' => $ventaData['numero_venta'] ?? $this->generarNumeroVenta(),
                'cliente_id' => $ventaData['cliente_id'] ?? null,
                'usuario_id' => $usuarioId,
                'tipo_venta' => $ventaData['tipo_venta'] ?? 'venta',
                'canal' => $ventaData['canal'] ?? 'mostrador',
                'estado' => $ventaData['estado'] ?? 'completada',
                'descuento' => $ventaData['descuento'] ?? 0,
                'reservado_hasta' => $ventaData['reservado_hasta'] ?? null,
                'fecha_venta' => $ventaData['fecha_venta'] ?? now(),
                'notas' => $ventaData['notas'] ?? null,
                'subtotal' => 0,
                'total' => 0,
                'monto_pagado' => 0,
                'estado_pago' => 'pendiente',
            ]);

            foreach ($lineas as $linea) {
                $this->agregarLinea($venta, $linea, $usuarioId);
            }

            return $this->recalcularTotales($venta);
        });
    }

    public function agregarLinea(Venta $venta, array $linea, int $usuarioId): DetalleVenta
    {
        $cantidad = (float) $linea['cantidad'];
        $precio = (float) ($linea['precio_unitario'] ?? 0);
        $descuento = (float) ($linea['descuento'] ?? 0);
        $subtotal = max(0, ($cantidad * $precio) - $descuento);

        if ($venta->estado === 'completada') {
            $asignaciones = $this->inventario->descontarPorVenta(
                $linea['producto_id'],
                $cantidad,
                Venta::class,
                $venta->id,
                $usuarioId
            );

            $loteId = $asignaciones[0]['lote']->id ?? null;
        } else {
            $loteId = $linea['lote_producto_id'] ?? null;
        }

        return DetalleVenta::query()->create([
            'venta_id' => $venta->id,
            'producto_id' => $linea['producto_id'],
            'lote_producto_id' => $loteId,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'descuento' => $descuento,
            'subtotal' => $subtotal,
        ]);
    }

    public function registrarPago(Venta $venta, float $monto, string $metodo, int $usuarioId, ?string $referencia = null, ?string $notas = null): Pago
    {
        return DB::transaction(function () use ($venta, $monto, $metodo, $usuarioId, $referencia, $notas) {
            if ($monto <= 0) {
                throw new InvalidArgumentException('El monto del pago debe ser mayor a cero.');
            }

            $pago = Pago::query()->create([
                'venta_id' => $venta->id,
                'monto' => $monto,
                'metodo_pago' => $metodo,
                'fecha_pago' => now(),
                'referencia' => $referencia,
                'notas' => $notas,
                'usuario_id' => $usuarioId,
            ]);

            $venta->increment('monto_pagado', $monto);
            $venta->refresh();

            if ((float) $venta->monto_pagado >= (float) $venta->total) {
                $venta->update(['estado_pago' => 'pagado']);
            } elseif ((float) $venta->monto_pagado > 0) {
                $venta->update(['estado_pago' => 'parcial']);
            }

            return $pago;
        });
    }

    public function anularVenta(Venta $venta, int $usuarioId): Venta
    {
        if ($venta->estado === 'cancelada') {
            throw new InvalidArgumentException('La venta ya está cancelada.');
        }

        return DB::transaction(function () use ($venta, $usuarioId) {
            foreach ($venta->detalles as $detalle) {
                if ($detalle->lote_producto_id) {
                    $lote = $detalle->lote;
                    if ($lote) {
                        $lote->increment('cantidad_disponible', $detalle->cantidad);
                    }

                    MovimientoInventario::query()->create([
                        'producto_id' => $detalle->producto_id,
                        'lote_producto_id' => $detalle->lote_producto_id,
                        'tipo_movimiento' => 'devolucion',
                        'cantidad' => $detalle->cantidad,
                        'referencia_tipo' => Venta::class,
                        'referencia_id' => $venta->id,
                        'notas' => 'Anulación de venta '.$venta->numero_venta,
                        'usuario_id' => $usuarioId,
                        'fecha_movimiento' => now(),
                    ]);
                }
            }

            $venta->update(['estado' => 'cancelada', 'estado_pago' => 'pendiente']);

            return $venta->fresh();
        });
    }

    public function recalcularTotales(Venta $venta): Venta
    {
        $venta->load('detalles');

        $subtotal = $venta->detalles->sum('subtotal');
        $total = max(0, $subtotal - (float) $venta->descuento);

        $estadoPago = 'pendiente';
        if ((float) $venta->monto_pagado >= $total && $total > 0) {
            $estadoPago = 'pagado';
        } elseif ((float) $venta->monto_pagado > 0) {
            $estadoPago = 'parcial';
        }

        $venta->update([
            'subtotal' => $subtotal,
            'total' => $total,
            'estado_pago' => $estadoPago,
        ]);

        return $venta->fresh();
    }
}
