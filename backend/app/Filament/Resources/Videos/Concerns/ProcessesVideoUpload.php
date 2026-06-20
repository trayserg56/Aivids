<?php

namespace App\Filament\Resources\Videos\Concerns;

use App\Jobs\ConvertVideoJob;
use App\Models\Video;
use App\Services\VideoUploadProcessor;
use Filament\Notifications\Notification;

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
        $relativePath = $processor->resolvePublicRelativePath($source);

        if ($absolutePath === null || ! is_file($absolutePath)) {
            return;
        }

        $video->markConversionQueued();

        ConvertVideoJob::dispatch(
            $video->id,
            $absolutePath,
            basename(is_array($source) ? $source[0] : $source),
            $relativePath,
        );

        Notification::make()
            ->title('Видео в обработке')
            ->body('Конвертация идёт в фоне. Прогресс — в списке кейсов.')
            ->success()
            ->send();
    }
}
