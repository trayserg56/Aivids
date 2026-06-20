<?php

namespace App\Filament\Resources\Videos\Concerns;

use App\Models\Video;
use App\Services\VideoUploadProcessor;
use Filament\Notifications\Notification;
use Throwable;

trait ProcessesVideoUpload
{
    protected function processUploadedSource(Video $video): void
    {
        $source = $this->form->getState()['source_video'] ?? null;

        if ($source === null || $source === []) {
            return;
        }

        $processor = app(VideoUploadProcessor::class);
        $absolutePath = $processor->resolvePublicPath($source);

        if ($absolutePath === null || ! is_file($absolutePath)) {
            return;
        }

        try {
            $processor->applyConversion(
                $video,
                $absolutePath,
                basename(is_array($source) ? $source[0] : $source),
            );
            $processor->deletePublicFile($source);

            Notification::make()
                ->title('Видео сконвертировано')
                ->body('Созданы mp4, превью и постер.')
                ->success()
                ->send();
        } catch (Throwable $exception) {
            Notification::make()
                ->title('Ошибка конвертации')
                ->body($exception->getMessage())
                ->danger()
                ->send();

            report($exception);
        }
    }
}
