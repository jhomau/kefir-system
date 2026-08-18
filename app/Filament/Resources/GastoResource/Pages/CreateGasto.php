<?php

namespace App\Filament\Resources\GastoResource\Pages;

use App\Filament\Resources\GastoResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGasto extends CreateRecord
{
    protected static string $resource = GastoResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['usuario_id'] = auth()->id();

        return parent::handleRecordCreation($data);
    }
}
