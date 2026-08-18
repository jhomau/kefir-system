<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermissions;
use App\Filament\Resources\GastoResource\Pages;
use App\Models\CategoriaGasto;
use App\Models\Gasto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GastoResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = Gasto::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Finanzas';

    protected static ?string $navigationLabel = 'Gastos';

    protected static ?string $modelLabel = 'gasto';

    protected static ?string $pluralModelLabel = 'gastos';

    protected static ?int $navigationSort = 1;

    protected static function permissionPrefix(): string
    {
        return 'gastos';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Gasto')->columns(2)->schema([
                Forms\Components\Select::make('categoria_gasto_id')
                    ->label('Categoría')
                    ->options(CategoriaGasto::query()->where('activo', true)->pluck('nombre', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('concepto')->label('Concepto')->required()->maxLength(200),
                Forms\Components\TextInput::make('monto')->label('Monto')->numeric()->required()->prefix('Bs.'),
                Forms\Components\DatePicker::make('fecha_gasto')->label('Fecha')->required()->default(now()),
                Forms\Components\Select::make('metodo_pago')
                    ->label('Método de pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'tarjeta' => 'Tarjeta',
                        'qr' => 'QR',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('nombre_proveedor')->label('Proveedor')->maxLength(150),
                Forms\Components\TextInput::make('referencia')->label('Referencia')->maxLength(100),
                Forms\Components\Textarea::make('notas')->label('Notas')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_gasto')->label('Fecha')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('concepto')->label('Concepto')->searchable(),
                Tables\Columns\TextColumn::make('categoria.nombre')->label('Categoría'),
                Tables\Columns\TextColumn::make('monto')->label('Monto')->money('BOB')->sortable(),
                Tables\Columns\TextColumn::make('metodo_pago')->label('Método')->badge(),
            ])
            ->defaultSort('fecha_gasto', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('categoria_gasto_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombre'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGastos::route('/'),
            'create' => Pages\CreateGasto::route('/create'),
            'edit' => Pages\EditGasto::route('/{record}/edit'),
        ];
    }
}
