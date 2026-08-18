<?php

namespace App\Services;

use App\Models\LoteProducto;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventarioService
{
    public function registrarProduccion(array $data, int $usuarioId): LoteProducto
    {
        return DB::transaction(function () use ($data, $usuarioId) {
            $lote = LoteProducto::query()->create([
                'producto_id' => $data['producto_id'],
                'codigo_lote' => $data['codigo_lote'],
                'fecha_produccion' => $data['fecha_produccion'],
                'fecha_vencimiento' => $data['fecha_vencimiento'],
                'cantidad_producida' => $data['cantidad'],
                'cantidad_disponible' => $data['cantidad'],
                'cantidad_reservada' => 0,
                'costo_produccion' => $data['costo_produccion'] ?? null,
                'notas' => $data['notas'] ?? null,
                'registrado_por' => $usuarioId,
            ]);

            $this->crearMovimiento([
                'producto_id' => $lote->producto_id,
                'lote_producto_id' => $lote->id,
                'tipo_movimiento' => 'produccion',
                'cantidad' => $data['cantidad'],
                'costo_unitario' => $data['costo_produccion'] ?? null,
                'notas' => $data['notas'] ?? null,
                'usuario_id' => $usuarioId,
            ]);

            return $lote;
        });
    }

    public function registrarAjuste(int $productoId, ?int $loteId, float $cantidad, ?string $notas, int $usuarioId): MovimientoInventario
    {
        if ($cantidad == 0) {
            throw new InvalidArgumentException('La cantidad del ajuste no puede ser cero.');
        }

        return DB::transaction(function () use ($productoId, $loteId, $cantidad, $notas, $usuarioId) {
            if ($loteId) {
                $lote = LoteProducto::query()->lockForUpdate()->findOrFail($loteId);
                $nuevaCantidad = (float) $lote->cantidad_disponible + $cantidad;

                if ($nuevaCantidad < 0) {
                    throw new InvalidArgumentException('Stock insuficiente en el lote para este ajuste.');
                }

                $lote->update(['cantidad_disponible' => $nuevaCantidad]);
            }

            return $this->crearMovimiento([
                'producto_id' => $productoId,
                'lote_producto_id' => $loteId,
                'tipo_movimiento' => 'ajuste',
                'cantidad' => $cantidad,
                'notas' => $notas,
                'usuario_id' => $usuarioId,
            ]);
        });
    }

    public function registrarMerma(int $productoId, int $loteId, float $cantidad, ?string $notas, int $usuarioId): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw new InvalidArgumentException('La merma debe ser mayor a cero.');
        }

        return DB::transaction(function () use ($productoId, $loteId, $cantidad, $notas, $usuarioId) {
            $lote = LoteProducto::query()->lockForUpdate()->findOrFail($loteId);

            if ((float) $lote->cantidad_disponible < $cantidad) {
                throw new InvalidArgumentException('Stock insuficiente en el lote para registrar merma.');
            }

            $lote->decrement('cantidad_disponible', $cantidad);

            return $this->crearMovimiento([
                'producto_id' => $productoId,
                'lote_producto_id' => $loteId,
                'tipo_movimiento' => 'merma',
                'cantidad' => -abs($cantidad),
                'notas' => $notas,
                'usuario_id' => $usuarioId,
            ]);
        });
    }

    /**
     * @return array<int, array{lote: LoteProducto, cantidad: float}>
     */
    public function descontarPorVenta(int $productoId, float $cantidad, string $referenciaTipo, int $referenciaId, int $usuarioId): array
    {
        if ($cantidad <= 0) {
            throw new InvalidArgumentException('La cantidad a descontar debe ser mayor a cero.');
        }

        return DB::transaction(function () use ($productoId, $cantidad, $referenciaTipo, $referenciaId, $usuarioId) {
            $pendiente = $cantidad;
            $asignaciones = [];

            $lotes = LoteProducto::query()
                ->where('producto_id', $productoId)
                ->where('cantidad_disponible', '>', 0)
                ->orderBy('fecha_vencimiento')
                ->lockForUpdate()
                ->get();

            foreach ($lotes as $lote) {
                if ($pendiente <= 0) {
                    break;
                }

                $disponible = (float) $lote->cantidad_disponible;
                $aDescontar = min($disponible, $pendiente);

                $lote->decrement('cantidad_disponible', $aDescontar);
                $pendiente -= $aDescontar;

                $this->crearMovimiento([
                    'producto_id' => $productoId,
                    'lote_producto_id' => $lote->id,
                    'tipo_movimiento' => 'venta',
                    'cantidad' => -$aDescontar,
                    'referencia_tipo' => $referenciaTipo,
                    'referencia_id' => $referenciaId,
                    'usuario_id' => $usuarioId,
                ]);

                $asignaciones[] = ['lote' => $lote->fresh(), 'cantidad' => $aDescontar];
            }

            if ($pendiente > 0) {
                throw new InvalidArgumentException('Stock insuficiente para completar la venta.');
            }

            return $asignaciones;
        });
    }

    public function stockProducto(int $productoId): float
    {
        return (float) LoteProducto::query()
            ->where('producto_id', $productoId)
            ->sum('cantidad_disponible');
    }

    public function lotesPorVencer(int $dias = 7): Collection
    {
        return LoteProducto::query()
            ->with('producto')
            ->where('cantidad_disponible', '>', 0)
            ->whereDate('fecha_vencimiento', '<=', now()->addDays($dias))
            ->orderBy('fecha_vencimiento')
            ->get();
    }

    public function productosBajoStock(): Collection
    {
        return Producto::query()
            ->where('activo', true)
            ->get()
            ->filter(fn (Producto $producto) => $this->stockProducto($producto->id) <= (float) $producto->stock_minimo);
    }

    protected function crearMovimiento(array $data): MovimientoInventario
    {
        return MovimientoInventario::query()->create([
            ...$data,
            'fecha_movimiento' => now(),
        ]);
    }
}
