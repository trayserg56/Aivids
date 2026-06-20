<?php

namespace App\Filament\Resources\Videos\Pages;

use App\Filament\Resources\Videos\Concerns\ProcessesVideoUpload;
use App\Filament\Resources\Videos\VideoResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateVideo extends CreateRecord
{
    use ProcessesVideoUpload;

    protected static string $resource = VideoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['poster_path'])) {
            $data['poster_path'] = 'posters/.pending';
        }

        return $data;
    }

    protected function beforeCreate(): void
    {
        $state = $this->form->getState();
        $hasSource = ! empty($state['source_video']);
        $hasExternal = ! empty($state['external_url']);

        if (! $hasSource && ! $hasExternal) {
            Notification::make()
                ->title('Добавьте видеофайл или внешнюю ссылку')
                ->danger()
                ->send();

            $this->halt();
        }

        if ($hasExternal && ! $hasSource && empty($state['poster_path'])) {
            Notification::make()
                ->title('Для внешней ссылки нужен постер')
                ->body('Загрузите изображение-постер или добавьте исходный видеофайл.')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        $this->processUploadedSource($this->record);
    }
}
