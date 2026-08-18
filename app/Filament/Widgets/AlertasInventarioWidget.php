<?php

namespace App\Filament\Widgets;

use App\Services\InventarioService;
use Filament\Widgets\Widget;

class AlertasInventarioWidget extends Widget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.alertas-inventario';

    protected function getViewData(): array
    {
        $inventario = app(InventarioService::class);

        return [
            'lotes' => $inventario->lotesPorVencer(7),
            'productosBajoStock' => $inventario->productosBajoStock(),
        ];
    }
}
