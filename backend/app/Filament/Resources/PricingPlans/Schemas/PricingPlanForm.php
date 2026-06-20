<?php

namespace App\Filament\Resources\PricingPlans\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PricingPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price_label')
                    ->label('Цена')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('от 15 000 ₽'),
                Textarea::make('description')
                    ->label('Подзаголовок')
                    ->required()
                    ->rows(2)
                    ->columnSpanFull(),
                TagsInput::make('features')
                    ->label('Пункты списка')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Enter — добавить пункт'),
                TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_recommended')
                    ->label('Бейдж «Рекомендуем»')
                    ->default(false),
            ]);
    }
}
