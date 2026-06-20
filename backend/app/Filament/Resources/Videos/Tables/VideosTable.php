<?php

namespace App\Filament\Resources\Videos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class VideosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->poll('3s')
            ->columns([
                ImageColumn::make('poster_path')
                    ->label('Постер')
                    ->disk('public')
                    ->height(56)
                    ->width(80),
                TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                ViewColumn::make('conversion_progress')
                    ->label('Обработка')
                    ->view('filament.tables.columns.video-conversion-progress'),
                TextColumn::make('categories')
                    ->label('Категории')
                    ->badge()
                    ->separator(','),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Опубликован')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Опубликован'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
