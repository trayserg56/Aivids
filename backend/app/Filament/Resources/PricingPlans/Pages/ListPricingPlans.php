<?php

namespace App\Filament\Resources\PricingPlans\Pages;

use App\Filament\Concerns\HasSectionSettingsAction;
use App\Filament\Resources\PricingPlans\PricingPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPricingPlans extends ListRecords
{
    use HasSectionSettingsAction;

    protected static string $resource = PricingPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->makeSectionSettingsAction('pricing', [
                'title' => 'Заголовок секции',
                'footer' => 'Текст под карточками',
            ], [
                'title' => 'Стоимость ИИ-видео',
                'footer' => 'Стоимость зависит от сложности проекта и длительности видео',
            ]),
            CreateAction::make()
                ->label('Добавить тариф'),
        ];
    }
}
