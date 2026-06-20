<?php

namespace App\Filament\Resources\Videos\Schemas;

use App\Support\VideoCategories;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Основное')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('URL-slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('categories')
                            ->label('Категории')
                            ->multiple()
                            ->options(array_combine(VideoCategories::OPTIONS, VideoCategories::OPTIONS))
                            ->default(['Портфолио'])
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->label('Порядок')
                            ->numeric()
                            ->default(0)
                            ->helperText('Меньше — выше в галерее'),
                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Видео')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('source_video')
                            ->label('Исходный видеофайл')
                            ->disk('public')
                            ->directory('uploads/raw')
                            ->acceptedFileTypes([
                                'video/mp4',
                                'video/quicktime',
                                'video/webm',
                                'video/x-msvideo',
                                'video/x-matroska',
                            ])
                            ->maxSize(512000)
                            ->dehydrated(false)
                            ->helperText('MP4, MOV, WEBM и др. После сохранения создаются mp4, превью и постер через ffmpeg.'),
                        FileUpload::make('poster_path')
                            ->label('Постер')
                            ->image()
                            ->directory('posters')
                            ->required(fn (Get $get): bool => filled($get('external_url')) && blank($get('source_video')))
                            ->visible(fn (Get $get, string $operation): bool => $operation === 'edit'
                                || (filled($get('external_url')) && blank($get('source_video')))),
                        TextInput::make('external_url')
                            ->label('Внешняя ссылка (YouTube/Vimeo)')
                            ->url()
                            ->columnSpanFull(),
                    ]),
                Section::make('Публикация')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Опубликован')
                            ->default(true),
                    ]),
            ]);
    }
}
