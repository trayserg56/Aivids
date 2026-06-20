<?php

namespace App\Filament\Resources\Stats\Pages;

use App\Filament\Concerns\HasSectionSettingsAction;
use App\Filament\Resources\Stats\StatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStats extends ListRecords
{
    use HasSectionSettingsAction;

    protected static string $resource = StatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->makeSectionSettingsAction('stats', [
                'title' => 'Заголовок секции',
            ], [
                'title' => 'Опыт в производстве ИИ-видео',
            ]),
            CreateAction::make()
                ->label('Добавить показатель'),
        ];
    }
}
