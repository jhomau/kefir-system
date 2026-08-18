<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Ventas';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'cliente';

    protected static ?string $pluralModelLabel = 'clientes';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del cliente')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->maxLength(30)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(150),
                        Forms\Components\Select::make('tipo_cliente')
                            ->label('Tipo')
                            ->options([
                                'persona' => 'Persona',
                                'negocio' => 'Negocio',
                            ])
                            ->required()
                            ->default('persona'),
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('correo')
                            ->label('Correo')
                            ->email()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('ciudad')
                            ->label('Ciudad')
                            ->maxLength(100),
                        Forms\Components\Textarea::make('direccion')
                            ->label('Dirección')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_cliente')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'persona' => 'Persona',
                        'negocio' => 'Negocio',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('correo')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ciudad')
                    ->label('Ciudad')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->defaultSort('nombre')
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_cliente')
                    ->label('Tipo')
                    ->options([
                        'persona' => 'Persona',
                        'negocio' => 'Negocio',
                    ]),
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
