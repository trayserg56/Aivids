<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use App\Support\ContactSources;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Имя')->disabled(),
                TextInput::make('email')->label('Email')->disabled(),
                TextInput::make('phone')->label('Телефон')->disabled(),
                TextInput::make('source_section')
                    ->label('Блок')
                    ->formatStateUsing(fn (?string $state): string => ContactSources::sectionLabel($state) ?? '—')
                    ->disabled(),
                TextInput::make('source_label')
                    ->label('Тариф / контекст')
                    ->disabled()
                    ->placeholder('—'),
                Textarea::make('message')->label('Сообщение')->disabled()->columnSpanFull(),
                Select::make('status')
                    ->label('Статус')
                    ->options([
                        'new' => 'Новая',
                        'in_progress' => 'В работе',
                        'done' => 'Закрыта',
                    ])
                    ->required(),
                TextInput::make('ip_address')->label('IP')->disabled(),
            ]);
    }
}
