<?php

namespace App\Filament\Resources\Benefits\Pages;

use App\Filament\Concerns\HasSectionSettingsAction;
use App\Filament\Resources\Benefits\BenefitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBenefits extends ListRecords
{
    use HasSectionSettingsAction;

    protected static string $resource = BenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->makeSectionSettingsAction('benefits', [
                'title' => 'Заголовок секции',
                'subtitle' => 'Подзаголовок',
            ], [
                'title' => 'Преимущества ИИ видеопроизводства',
                'subtitle' => 'Видео создаётся без студии, актёров и сложной организации съёмок.',
            ]),
            CreateAction::make()
                ->label('Добавить преимущество'),
        ];
    }
}
