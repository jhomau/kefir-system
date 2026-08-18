<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Productos';

    protected static ?string $modelLabel = 'producto';

    protected static ?string $pluralModelLabel = 'productos';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del producto')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('codigo_producto')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(150),
                        Forms\Components\Select::make('unidad_medida')
                            ->label('Unidad de medida')
                            ->options([
                                'unidad' => 'Unidad',
                                'litro' => 'Litro',
                                'botella' => 'Botella',
                            ])
                            ->required()
                            ->default('unidad'),
                        Forms\Components\TextInput::make('precio_venta')
                            ->label('Precio de venta')
                            ->required()
                            ->numeric()
                            ->prefix('Bs.')
                            ->minValue(0),
                        Forms\Components\TextInput::make('precio_costo')
                            ->label('Precio de costo')
                            ->numeric()
                            ->prefix('Bs.')
                            ->minValue(0),
                        Forms\Components\TextInput::make('stock_minimo')
                            ->label('Stock mínimo')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                        Forms\Components\Toggle::make('vendible_online')
                            ->label('Vendible en tienda web')
                            ->default(false),
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_producto')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unidad_medida')
                    ->label('Unidad')
                    ->badge(),
                Tables\Columns\TextColumn::make('precio_venta')
                    ->label('Precio venta')
                    ->money('BOB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_disponible')
                    ->label('Stock')
                    ->getStateUsing(fn (Producto $record): string => number_format($record->stockDisponible(), 2))
                    ->color(fn (Producto $record): ?string => $record->stockDisponible() <= (float) $record->stock_minimo ? 'danger' : null),
                Tables\Columns\TextColumn::make('stock_minimo')
                    ->label('Stock mín.')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('vendible_online')
                    ->label('Web')
                    ->boolean(),
            ])
            ->defaultSort('nombre')
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo'),
                Tables\Filters\TrashedFilter::make()
                    ->label('Eliminados'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
