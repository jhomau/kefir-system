<?php

namespace App\Filament\Resources\OtroIngresoResource\Pages;

use App\Filament\Resources\OtroIngresoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOtroIngreso extends EditRecord
{
    protected static string $resource = OtroIngresoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
