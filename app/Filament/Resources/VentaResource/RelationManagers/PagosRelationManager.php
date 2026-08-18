<?php

namespace App\Filament\Resources\VentaResource\RelationManagers;

use App\Services\VentaService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PagosRelationManager extends RelationManager
{
    protected static string $relationship = 'pagos';

    protected static ?string $title = 'Pagos';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('monto')
                ->label('Monto')
                ->numeric()
                ->required()
                ->prefix('Bs.'),
            Forms\Components\Select::make('metodo_pago')
                ->label('Método')
                ->options([
                    'efectivo' => 'Efectivo',
                    'transferencia' => 'Transferencia',
                    'tarjeta' => 'Tarjeta',
                    'qr' => 'QR',
                ])
                ->required(),
            Forms\Components\TextInput::make('referencia')
                ->label('Referencia')
                ->maxLength(100),
            Forms\Components\Textarea::make('notas')
                ->label('Notas'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_pago')->label('Fecha')->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('monto')->label('Monto')->money('BOB'),
                Tables\Columns\TextColumn::make('metodo_pago')->label('Método')->badge(),
                Tables\Columns\TextColumn::make('referencia')->label('Referencia'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar pago')
                    ->visible(fn () => auth()->user()?->can('pagos.registrar'))
                    ->using(function (array $data, RelationManager $livewire) {
                        return app(VentaService::class)->registrarPago(
                            $livewire->getOwnerRecord(),
                            (float) $data['monto'],
                            $data['metodo_pago'],
                            auth()->id(),
                            $data['referencia'] ?? null,
                            $data['notas'] ?? null
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->visible(false),
                Tables\Actions\EditAction::make()->visible(false),
            ]);
    }
}
