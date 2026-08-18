<?php

namespace App\Filament\Resources\MovimientoInventarioResource\Pages;

use App\Filament\Resources\MovimientoInventarioResource;
use App\Services\InventarioService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMovimientoInventario extends CreateRecord
{
    protected static string $resource = MovimientoInventarioResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $service = app(InventarioService::class);

        if ($data['tipo_movimiento'] === 'merma') {
            return $service->registrarMerma(
                $data['producto_id'],
                $data['lote_producto_id'],
                abs((float) $data['cantidad']),
                $data['notas'] ?? null,
                auth()->id()
            );
        }

        return $service->registrarAjuste(
            $data['producto_id'],
            $data['lote_producto_id'] ?? null,
            (float) $data['cantidad'],
            $data['notas'] ?? null,
            auth()->id()
        );
    }
}
