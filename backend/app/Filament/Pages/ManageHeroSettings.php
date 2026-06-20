<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\LandingDefaults;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageHeroSettings extends Page
{
    /** @var array<string, mixed>|null */
    public ?array $data = [];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Первый экран';

    protected static ?string $title = 'Первый экран';

    protected static ?int $navigationSort = 1;

    protected static string|UnitEnum|null $navigationGroup = 'Главная';

    public function mount(): void
    {
        $this->form->fill(SiteSetting::get('hero', LandingDefaults::hero()));
    }

    /** @return array<string, string> */
    public static function defaults(): array
    {
        return LandingDefaults::hero();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('badge')
                    ->label('Бейдж')
                    ->required()
                    ->maxLength(255),
                TextInput::make('title')
                    ->label('Заголовок')
                    ->required()
                    ->maxLength(255),
                Textarea::make('subtitle')
                    ->label('Подзаголовок')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('cta')
                    ->label('Текст кнопки')
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Сохранить')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ])->alignment(Alignment::Start),
                ]),
        ]);
    }

    public function save(): void
    {
        SiteSetting::set('hero', $this->form->getState());

        Notification::make()
            ->title('Сохранено')
            ->success()
            ->send();
    }
}
