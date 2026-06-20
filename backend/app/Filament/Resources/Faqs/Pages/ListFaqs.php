<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Concerns\HasSectionSettingsAction;
use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFaqs extends ListRecords
{
    use HasSectionSettingsAction;

    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->makeSectionSettingsAction('faq', [
                'title' => 'Заголовок секции',
            ], [
                'title' => 'Часто задаваемые вопросы',
            ]),
            CreateAction::make()
                ->label('Добавить вопрос'),
        ];
    }
}
