<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Services\VentaService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenta extends EditRecord
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('anular')
                ->label('Anular venta')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->estado !== 'cancelada' && auth()->user()?->can('ventas.anular'))
                ->action(function () {
                    app(VentaService::class)->anularVenta($this->record, auth()->id());
                    $this->refreshFormData(['estado', 'estado_pago']);
                }),
            Actions\DeleteAction::make()->visible(false),
        ];
    }
}
