<?php

namespace App\Filament\Widgets;

use App\Models\Gasto;
use App\Models\OtroIngreso;
use App\Models\Venta;
use App\Services\InventarioService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KefirStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $inventario = app(InventarioService::class);
        $inicioMes = now()->startOfMonth();

        $ventasMes = (float) Venta::query()
            ->where('estado', 'completada')
            ->where('fecha_venta', '>=', $inicioMes)
            ->sum('total');

        $gastosMes = (float) Gasto::query()
            ->where('fecha_gasto', '>=', $inicioMes)
            ->sum('monto');

        $ingresosMes = (float) OtroIngreso::query()
            ->where('fecha_ingreso', '>=', $inicioMes)
            ->sum('monto');

        return [
            Stat::make('Ventas del mes', 'Bs. '.number_format($ventasMes, 2))
                ->description('Ventas completadas')
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Gastos del mes', 'Bs. '.number_format($gastosMes, 2))
                ->description('Total registrado')
                ->color('danger')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Balance estimado', 'Bs. '.number_format($ventasMes + $ingresosMes - $gastosMes, 2))
                ->description('Ventas + ingresos - gastos')
                ->color('primary')
                ->icon('heroicon-o-calculator'),
            Stat::make('Alertas inventario', (string) ($inventario->lotesPorVencer(7)->count() + $inventario->productosBajoStock()->count()))
                ->description('Vencimientos + stock bajo')
                ->color('warning')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
