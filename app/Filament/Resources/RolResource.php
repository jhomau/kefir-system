<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolResource\Pages;
use App\Models\Rol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RolResource extends Resource
{
    protected static ?string $model = Rol::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Seguridad';

    protected static ?string $navigationLabel = 'Roles';

    protected static ?string $modelLabel = 'rol';

    protected static ?string $pluralModelLabel = 'roles';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'roles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del rol')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Identificador')
                            ->required()
                            ->alphaDash()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?Rol $record): bool => $record?->es_sistema ?? false)
                            ->dehydrated(),
                        Forms\Components\TextInput::make('nombre_visible')
                            ->label('Nombre visible')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Hidden::make('guard_name')
                            ->default('web'),
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ]),
                Forms\Components\Section::make('Permisos')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Permisos asignados')
                            ->relationship('permissions', 'nombre_visible')
                            ->columns(3)
                            ->searchable()
                            ->bulkToggleable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_visible')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Identificador')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Permisos')
                    ->counts('permissions')
                    ->sortable(),
                Tables\Columns\IconColumn::make('es_sistema')
                    ->label('Sistema')
                    ->boolean(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->defaultSort('nombre_visible')
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (Rol $record): bool => ! $record->es_sistema),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRols::route('/'),
            'create' => Pages\CreateRol::route('/create'),
            'edit' => Pages\EditRol::route('/{record}/edit'),
        ];
    }
}
