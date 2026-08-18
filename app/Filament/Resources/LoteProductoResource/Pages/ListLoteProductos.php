<?php

namespace App\Filament\Resources\LoteProductoResource\Pages;

use App\Filament\Resources\LoteProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoteProductos extends ListRecords
{
    protected static string $resource = LoteProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
