<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\VideoUploadProcessor;
use App\Support\VideoConversionStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ConvertVideoJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3700;

    public int $tries = 1;

    public function __construct(
        public int $videoId,
        public string $sourceAbsolutePath,
        public ?string $sourceFilename = null,
        public ?string $rawUploadRelativePath = null,
    ) {}

    public function handle(VideoUploadProcessor $processor): void
    {
        $video = Video::query()->findOrFail($this->videoId);

        $video->markConversionProcessing('Подготовка');

        try {
            $processor->applyConversion(
                $video,
                $this->sourceAbsolutePath,
                $this->sourceFilename,
                fn (int $progress, string $step) => $video->updateConversionProgress($progress, $step),
            );

            if ($this->rawUploadRelativePath) {
                $processor->deletePublicFile($this->rawUploadRelativePath);
            }

            $video->markConversionCompleted();
            Cache::forget('home.page');
        } catch (Throwable $exception) {
            $video->markConversionFailed($exception->getMessage());

            report($exception);

            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        Video::query()
            ->whereKey($this->videoId)
            ->where('conversion_status', VideoConversionStatus::Processing)
            ->update([
                'conversion_status' => VideoConversionStatus::Failed,
                'conversion_step' => 'Ошибка',
            ]);
    }
}
