<?php

namespace App\Filament\Pages;

use App\Models\Gasto;
use App\Models\OtroIngreso;
use App\Models\Venta;
use Filament\Pages\Page;

class Reportes extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Finanzas';

    protected static ?string $navigationLabel = 'Reportes';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.reportes';

    protected static ?string $title = 'Reportes';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('administrador') || $user->can('reportes.ver'));
    }

    public function getViewData(): array
    {
        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        $ventas = Venta::query()
            ->where('estado', 'completada')
            ->whereBetween('fecha_venta', [$inicioMes, $finMes])
            ->get();

        $gastos = Gasto::query()
            ->whereBetween('fecha_gasto', [$inicioMes, $finMes])
            ->with('categoria')
            ->get();

        $ingresos = OtroIngreso::query()
            ->whereBetween('fecha_ingreso', [$inicioMes, $finMes])
            ->get();

        $totalVentas = $ventas->sum('total');
        $totalGastos = $gastos->sum('monto');
        $totalIngresos = $ingresos->sum('monto');

        return [
            'periodo' => $inicioMes->translatedFormat('F Y'),
            'totalVentas' => $totalVentas,
            'totalGastos' => $totalGastos,
            'totalIngresos' => $totalIngresos,
            'utilidad' => $totalVentas + $totalIngresos - $totalGastos,
            'cantidadVentas' => $ventas->count(),
            'gastosPorCategoria' => $gastos->groupBy(fn ($g) => $g->categoria->nombre)->map->sum('monto'),
            'ventasRecientes' => $ventas->sortByDesc('fecha_venta')->take(10),
        ];
    }
}
