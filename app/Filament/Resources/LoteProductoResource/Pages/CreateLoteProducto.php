<?php

namespace App\Filament\Resources\LoteProductoResource\Pages;

use App\Filament\Resources\LoteProductoResource;
use App\Services\InventarioService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLoteProducto extends CreateRecord
{
    protected static string $resource = LoteProductoResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(InventarioService::class)->registrarProduccion([
            'producto_id' => $data['producto_id'],
            'codigo_lote' => $data['codigo_lote'],
            'fecha_produccion' => $data['fecha_produccion'],
            'fecha_vencimiento' => $data['fecha_vencimiento'],
            'cantidad' => $data['cantidad_producida'],
            'costo_produccion' => $data['costo_produccion'] ?? null,
            'notas' => $data['notas'] ?? null,
        ], auth()->id());
    }
}
