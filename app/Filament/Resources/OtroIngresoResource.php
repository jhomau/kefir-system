<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermissions;
use App\Filament\Resources\OtroIngresoResource\Pages;
use App\Models\OtroIngreso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OtroIngresoResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = OtroIngreso::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationGroup = 'Finanzas';

    protected static ?string $navigationLabel = 'Otros ingresos';

    protected static ?string $modelLabel = 'ingreso';

    protected static ?string $pluralModelLabel = 'otros ingresos';

    protected static ?int $navigationSort = 2;

    protected static function permissionPrefix(): string
    {
        return 'gastos';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ingreso')->columns(2)->schema([
                Forms\Components\TextInput::make('concepto')->label('Concepto')->required()->maxLength(200),
                Forms\Components\TextInput::make('monto')->label('Monto')->numeric()->required()->prefix('Bs.'),
                Forms\Components\DatePicker::make('fecha_ingreso')->label('Fecha')->required()->default(now()),
                Forms\Components\Select::make('metodo_pago')
                    ->label('Método')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'tarjeta' => 'Tarjeta',
                        'qr' => 'QR',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notas')->label('Notas')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_ingreso')->label('Fecha')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('concepto')->label('Concepto')->searchable(),
                Tables\Columns\TextColumn::make('monto')->label('Monto')->money('BOB')->sortable(),
                Tables\Columns\TextColumn::make('metodo_pago')->label('Método')->badge(),
            ])
            ->defaultSort('fecha_ingreso', 'desc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOtroIngresos::route('/'),
            'create' => Pages\CreateOtroIngreso::route('/create'),
            'edit' => Pages\EditOtroIngreso::route('/{record}/edit'),
        ];
    }
}
