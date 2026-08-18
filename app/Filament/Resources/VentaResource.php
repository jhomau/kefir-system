<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermissions;
use App\Filament\Resources\VentaResource\Pages;
use App\Filament\Resources\VentaResource\RelationManagers\PagosRelationManager;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VentaResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Ventas';

    protected static ?string $navigationLabel = 'Ventas';

    protected static ?string $modelLabel = 'venta';

    protected static ?string $pluralModelLabel = 'ventas';

    protected static ?int $navigationSort = 2;

    protected static function permissionPrefix(): string
    {
        return 'ventas';
    }

    public static function canDelete($record): bool
    {
        return static::userCan('anular');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos de la venta')->columns(2)->schema([
                Forms\Components\TextInput::make('numero_venta')
                    ->label('Número')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
                Forms\Components\Select::make('cliente_id')
                    ->label('Cliente')
                    ->options(Cliente::query()->where('activo', true)->pluck('nombre', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('tipo_venta')
                    ->label('Tipo')
                    ->options([
                        'venta' => 'Venta',
                        'reserva' => 'Reserva',
                        'pedido_web' => 'Pedido web',
                    ])
                    ->default('venta')
                    ->required(),
                Forms\Components\Select::make('canal')
                    ->label('Canal')
                    ->options([
                        'mostrador' => 'Mostrador',
                        'telefono' => 'Teléfono',
                        'web' => 'Web',
                        'whatsapp' => 'WhatsApp',
                    ])
                    ->default('mostrador')
                    ->required(),
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'completada' => 'Completada',
                        'reservada' => 'Reservada',
                        'cancelada' => 'Cancelada',
                    ])
                    ->default('completada')
                    ->required(),
                Forms\Components\TextInput::make('descuento')
                    ->label('Descuento total')
                    ->numeric()
                    ->default(0)
                    ->prefix('Bs.'),
                Forms\Components\DateTimePicker::make('fecha_venta')
                    ->label('Fecha')
                    ->default(now()),
                Forms\Components\Textarea::make('notas')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Productos')
                ->schema([
                    Forms\Components\Repeater::make('detalles')
                        ->label('Detalle')
                        ->schema([
                            Forms\Components\Select::make('producto_id')
                                ->label('Producto')
                                ->options(Producto::query()->where('activo', true)->pluck('nombre', 'id'))
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    if ($state) {
                                        $producto = Producto::find($state);
                                        $set('precio_unitario', $producto?->precio_venta);
                                    }
                                }),
                            Forms\Components\TextInput::make('cantidad')
                                ->label('Cantidad')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(0.001),
                            Forms\Components\TextInput::make('precio_unitario')
                                ->label('Precio unit.')
                                ->numeric()
                                ->required()
                                ->prefix('Bs.'),
                            Forms\Components\TextInput::make('descuento')
                                ->label('Descuento')
                                ->numeric()
                                ->default(0)
                                ->prefix('Bs.'),
                        ])
                        ->columns(4)
                        ->defaultItems(1)
                        ->minItems(1)
                        ->visibleOn('create'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_venta')->label('Número')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('tipo_venta')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('estado')->label('Estado')->badge(),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('BOB')->sortable(),
                Tables\Columns\TextColumn::make('estado_pago')->label('Pago')->badge(),
                Tables\Columns\TextColumn::make('fecha_venta')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('fecha_venta', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')->label('Estado')->options([
                    'completada' => 'Completada',
                    'reservada' => 'Reservada',
                    'cancelada' => 'Cancelada',
                ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getRelations(): array
    {
        return [
            PagosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}
