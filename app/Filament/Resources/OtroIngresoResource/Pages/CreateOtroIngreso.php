<?php

namespace App\Filament\Resources\OtroIngresoResource\Pages;

use App\Filament\Resources\OtroIngresoResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOtroIngreso extends CreateRecord
{
    protected static string $resource = OtroIngresoResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['usuario_id'] = auth()->id();

        return parent::handleRecordCreation($data);
    }
}
