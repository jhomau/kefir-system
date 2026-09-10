<?php

namespace App\Filament\Pages;

use App\Models\Configuracion;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class Ajustes extends Page implements HasForms
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Ajustes';

    protected static ?int $navigationSort = 90;

    protected static ?string $slug = 'ajustes';

    protected static ?string $title = 'Ajustes';

    protected static string $view = 'filament.pages.ajustes';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('administrador') || $user->can('configuracion.editar'));
    }

    public function mount(): void
    {
        $this->form->fill(Configuracion::actual()->only([
            'nombre_negocio',
            'eslogan',
            'telefono',
            'whatsapp',
            'correo',
            'direccion',
            'horario',
            'mensaje_tienda',
        ]));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Negocio')
                    ->description('Estos datos aparecen en el panel y en la tienda web.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nombre_negocio')
                            ->label('Nombre del negocio')
                            ->required()
                            ->maxLength(120),
                        Forms\Components\TextInput::make('eslogan')
                            ->label('Eslogan')
                            ->maxLength(180),
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(30),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(30)
                            ->helperText('Incluye código de país, por ejemplo 5917XXXXXXX.'),
                        Forms\Components\TextInput::make('correo')
                            ->label('Correo')
                            ->email()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('horario')
                            ->label('Horario')
                            ->maxLength(150)
                            ->placeholder('Lun-Sab 8:00-18:00'),
                        Forms\Components\Textarea::make('direccion')
                            ->label('Dirección')
                            ->rows(2)
                            ->columnSpanFull()
                            ->maxLength(500),
                    ]),
                Forms\Components\Section::make('Tienda web')
                    ->schema([
                        Forms\Components\Textarea::make('mensaje_tienda')
                            ->label('Mensaje del catálogo')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Texto que se muestra debajo del título en /tienda.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function guardar(): void
    {
        $ajustes = Configuracion::actual();
        $ajustes->fill($this->form->getState());
        $ajustes->save();

        Notification::make()
            ->title('Ajustes guardados')
            ->success()
            ->send();
    }

    public function getFormActions(): array
    {
        return [
            Action::make('guardar')
                ->label('Guardar ajustes')
                ->submit('guardar'),
        ];
    }
}
