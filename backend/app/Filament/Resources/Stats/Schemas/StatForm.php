<?php

namespace App\Filament\Resources\Stats\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('value')
                    ->label('Значение')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('130+'),
                TextInput::make('label')
                    ->label('Подпись')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0)
                    ->helperText('Меньше — левее'),
                Toggle::make('is_highlighted')
                    ->label('Синяя карточка')
                    ->default(false),
            ]);
    }
}
