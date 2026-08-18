<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermissions;
use App\Filament\Resources\MovimientoInventarioResource\Pages;
use App\Models\LoteProducto;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Services\InventarioService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MovimientoInventarioResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = MovimientoInventario::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Movimientos';

    protected static ?string $modelLabel = 'movimiento';

    protected static ?string $pluralModelLabel = 'movimientos';

    protected static ?int $navigationSort = 2;

    protected static function permissionPrefix(): string
    {
        return 'inventario';
    }

    public static function canCreate(): bool
    {
        return static::userCan('ajustar') || static::userCan('merma');
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Registrar movimiento')->columns(2)->schema([
                Forms\Components\Select::make('tipo_movimiento')
                    ->label('Tipo')
                    ->options([
                        'ajuste' => 'Ajuste',
                        'merma' => 'Merma',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Select::make('producto_id')
                    ->label('Producto')
                    ->options(Producto::query()->where('activo', true)->pluck('nombre', 'id'))
                    ->searchable()
                    ->required()
                    ->live(),
                Forms\Components\Select::make('lote_producto_id')
                    ->label('Lote')
                    ->options(fn (Forms\Get $get) => LoteProducto::query()
                        ->where('producto_id', $get('producto_id'))
                        ->where('cantidad_disponible', '>', 0)
                        ->pluck('codigo_lote', 'id'))
                    ->searchable()
                    ->required(fn (Forms\Get $get) => $get('tipo_movimiento') === 'merma'),
                Forms\Components\TextInput::make('cantidad')
                    ->label('Cantidad')
                    ->helperText('Para ajustes use positivo (entrada) o negativo (salida).')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('notas')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_movimiento')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('tipo_movimiento')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('producto.nombre')->label('Producto')->searchable(),
                Tables\Columns\TextColumn::make('lote.codigo_lote')->label('Lote')->toggleable(),
                Tables\Columns\TextColumn::make('cantidad')->label('Cantidad')->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('usuario.nombre')->label('Usuario')->toggleable(),
            ])
            ->defaultSort('fecha_movimiento', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_movimiento')
                    ->label('Tipo')
                    ->options([
                        'produccion' => 'Producción',
                        'venta' => 'Venta',
                        'ajuste' => 'Ajuste',
                        'merma' => 'Merma',
                        'devolucion' => 'Devolución',
                    ]),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMovimientoInventarios::route('/'),
            'create' => Pages\CreateMovimientoInventario::route('/create'),
        ];
    }
}
