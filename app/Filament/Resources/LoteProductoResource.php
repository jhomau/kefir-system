<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermissions;
use App\Filament\Resources\LoteProductoResource\Pages;
use App\Models\LoteProducto;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoteProductoResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = LoteProducto::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Lotes';

    protected static ?string $modelLabel = 'lote';

    protected static ?string $pluralModelLabel = 'lotes';

    protected static ?int $navigationSort = 1;

    protected static function permissionPrefix(): string
    {
        return 'inventario';
    }

    public static function canCreate(): bool
    {
        return static::userCan('registrar_produccion');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Producción de lote')->columns(2)->schema([
                Forms\Components\Select::make('producto_id')
                    ->label('Producto')
                    ->options(Producto::query()->where('activo', true)->pluck('nombre', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('codigo_lote')
                    ->label('Código de lote')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                Forms\Components\DatePicker::make('fecha_produccion')
                    ->label('Fecha producción')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('fecha_vencimiento')
                    ->label('Fecha vencimiento')
                    ->required(),
                Forms\Components\TextInput::make('cantidad_producida')
                    ->label('Cantidad producida')
                    ->numeric()
                    ->required()
                    ->minValue(0.001),
                Forms\Components\TextInput::make('costo_produccion')
                    ->label('Costo de producción')
                    ->numeric()
                    ->prefix('Bs.')
                    ->minValue(0),
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
                Tables\Columns\TextColumn::make('codigo_lote')->label('Lote')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('producto.nombre')->label('Producto')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cantidad_disponible')->label('Disponible')->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('fecha_vencimiento')->label('Vence')->date('d/m/Y')->sortable()
                    ->color(fn (LoteProducto $record) => $record->fecha_vencimiento->isPast() ? 'danger' : ($record->fecha_vencimiento->lte(now()->addDays(7)) ? 'warning' : null)),
                Tables\Columns\TextColumn::make('fecha_produccion')->label('Producción')->date('d/m/Y')->toggleable(),
                Tables\Columns\TextColumn::make('registradoPor.nombre')->label('Registrado por')->toggleable(),
            ])
            ->defaultSort('fecha_vencimiento')
            ->filters([
                Tables\Filters\SelectFilter::make('producto_id')
                    ->label('Producto')
                    ->relationship('producto', 'nombre'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(false),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoteProductos::route('/'),
            'create' => Pages\CreateLoteProducto::route('/create'),
        ];
    }
}
