<?php

namespace App\Filament\Resources\LoteProductoResource\Pages;

use App\Filament\Resources\LoteProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoteProducto extends EditRecord
{
    protected static string $resource = LoteProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
