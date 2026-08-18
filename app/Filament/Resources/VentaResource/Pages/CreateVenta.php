<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Services\VentaService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $detalles = $data['detalles'] ?? [];
        unset($data['detalles']);

        return app(VentaService::class)->crearVenta($data, $detalles, auth()->id());
    }
}
