<?php

namespace App\Filament\Resources\ContactSubmissions\Tables;

use App\Support\ContactSources;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Дата')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('name')->label('Имя')->searchable(),
                TextColumn::make('source_section')
                    ->label('Блок')
                    ->formatStateUsing(fn (?string $state): string => ContactSources::sectionLabel($state) ?? '—')
                    ->sortable(),
                TextColumn::make('source_label')
                    ->label('Тариф')
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('phone')->label('Телефон'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('message')->label('Сообщение')->limit(40),
                TextColumn::make('status')->label('Статус')->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
