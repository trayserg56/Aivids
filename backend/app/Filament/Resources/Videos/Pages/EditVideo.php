<?php

namespace App\Filament\Resources\Videos\Pages;

use App\Filament\Resources\Videos\Concerns\ProcessesVideoUpload;
use App\Filament\Resources\Videos\VideoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVideo extends EditRecord
{
    use ProcessesVideoUpload;

    protected static string $resource = VideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->processUploadedSource($this->record);
    }
}
