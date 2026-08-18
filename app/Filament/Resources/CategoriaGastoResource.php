<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermissions;
use App\Filament\Resources\CategoriaGastoResource\Pages;
use App\Models\CategoriaGasto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoriaGastoResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = CategoriaGasto::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Finanzas';

    protected static ?string $navigationLabel = 'Categorías de gasto';

    protected static ?string $modelLabel = 'categoría';

    protected static ?string $pluralModelLabel = 'categorías';

    protected static ?int $navigationSort = 3;

    protected static function permissionPrefix(): string
    {
        return 'gastos';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')->label('Nombre')->required()->maxLength(100),
            Forms\Components\Toggle::make('activo')->label('Activo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('activo')->label('Activo')->boolean(),
                Tables\Columns\TextColumn::make('gastos_count')->label('Gastos')->counts('gastos'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategoriaGastos::route('/'),
            'create' => Pages\CreateCategoriaGasto::route('/create'),
            'edit' => Pages\EditCategoriaGasto::route('/{record}/edit'),
        ];
    }
}
