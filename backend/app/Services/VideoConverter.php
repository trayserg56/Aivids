<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class VideoConverter
{
    private const VIDEO_EXTENSIONS = ['mp4', 'mov', 'webm', 'mkv', 'avi', 'm4v'];

    public function isVideoFile(string $path): bool
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::VIDEO_EXTENSIONS, true);
    }

    /**
     * @param  callable(int $progress, string $step): void|null  $onProgress
     * @return array{video_path: string, preview_path: string, poster_path: string, file_size_bytes: int, width: int, height: int}
     */
    public function convert(string $inputPath, string $slug, ?callable $onProgress = null): array
    {
        if (! is_file($inputPath)) {
            throw new RuntimeException("Source file not found: {$inputPath}");
        }

        [$width, $height] = $this->probeDimensions($inputPath);
        $duration = $this->probeDuration($inputPath);

        $disk = Storage::disk('public');
        $disk->makeDirectory('videos');
        $disk->makeDirectory('posters');

        $videoRelative = "videos/{$slug}.mp4";
        $previewRelative = "videos/{$slug}-preview.mp4";
        $posterRelative = "posters/{$slug}.webp";

        $videoAbsolute = $disk->path($videoRelative);
        $previewAbsolute = $disk->path($previewRelative);
        $posterAbsolute = $disk->path($posterRelative);

        if ($onProgress) {
            $onProgress(2, 'Основное видео');
        }

        $this->runFfmpegWithProgress([
            'ffmpeg', '-y', '-i', $inputPath,
            '-vf', "scale='min(1920,iw)':'-2'",
            '-c:v', 'libx264', '-crf', '28', '-preset', 'medium',
            '-movflags', '+faststart',
            '-c:a', 'aac', '-b:a', '128k', '-ac', '2',
            $videoAbsolute,
        ], 'Основное видео', 5, 72, $duration, $onProgress);

        $this->runFfmpegWithProgress([
            'ffmpeg', '-y', '-i', $inputPath,
            '-vf', "scale='min(854,iw)':'-2'",
            '-c:v', 'libx264', '-crf', '32', '-preset', 'fast',
            '-movflags', '+faststart',
            '-an',
            $previewAbsolute,
        ], 'Превью', 72, 90, $duration, $onProgress);

        if ($onProgress) {
            $onProgress(92, 'Постер');
        }

        $this->runFfmpeg([
            'ffmpeg', '-y', '-ss', '00:00:01', '-i', $inputPath,
            '-vframes', '1',
            '-vf', "scale='min(1280,iw)':'-2'",
            '-c:v', 'libwebp', '-quality', '80',
            $posterAbsolute,
        ], 'poster');

        if ($onProgress) {
            $onProgress(100, 'Готово');
        }

        return [
            'video_path' => $videoRelative,
            'preview_path' => $previewRelative,
            'poster_path' => $posterRelative,
            'file_size_bytes' => (int) filesize($videoAbsolute),
            'width' => $width,
            'height' => $height,
        ];
    }

    /** @return array{0: int, 1: int} */
    public function probeDimensions(string $path): array
    {
        $result = Process::timeout(60)->run([
            'ffprobe',
            '-v', 'error',
            '-select_streams', 'v:0',
            '-show_entries', 'stream=width,height',
            '-of', 'csv=s=x:p=0',
            $path,
        ]);

        if (! $result->successful()) {
            return [1920, 1080];
        }

        $output = trim($result->output());

        if (preg_match('/^(\d+)x(\d+)$/', $output, $matches)) {
            return [(int) $matches[1], (int) $matches[2]];
        }

        return [1920, 1080];
    }

    public function probeDuration(string $path): float
    {
        $result = Process::timeout(60)->run([
            'ffprobe',
            '-v', 'error',
            '-show_entries', 'format=duration',
            '-of', 'csv=p=0',
            $path,
        ]);

        if (! $result->successful()) {
            return 0.0;
        }

        return max(0.0, (float) trim($result->output()));
    }

    public function titleFromFilename(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);

        return Str::title(str_replace(['_', '-'], ' ', $name));
    }

    public function slugFromFilename(string $filename): string
    {
        return Str::slug(pathinfo($filename, PATHINFO_FILENAME));
    }

    /** @param  callable(int $progress, string $step): void|null  $onProgress */
    private function runFfmpegWithProgress(
        array $command,
        string $stepLabel,
        int $rangeStart,
        int $rangeEnd,
        float $sourceDuration,
        ?callable $onProgress,
    ): void {
        $outputFile = array_pop($command);
        $command[] = '-progress';
        $command[] = 'pipe:1';
        $command[] = '-nostats';
        $command[] = $outputFile;

        $lastReported = -1;

        $result = Process::timeout(3600)->run($command, function (string $type, string $buffer) use (
            $onProgress,
            $stepLabel,
            $rangeStart,
            $rangeEnd,
            $sourceDuration,
            &$lastReported,
        ): void {
            if ($onProgress === null) {
                return;
            }

            foreach (explode("\n", $buffer) as $line) {
                if (! str_starts_with($line, 'out_time_us=')) {
                    continue;
                }

                $microseconds = (int) substr($line, strlen('out_time_us='));

                if ($sourceDuration <= 0) {
                    continue;
                }

                $ratio = min(1.0, ($microseconds / 1_000_000) / $sourceDuration);
                $progress = (int) round($rangeStart + ($rangeEnd - $rangeStart) * $ratio);

                if ($progress <= $lastReported) {
                    continue;
                }

                $lastReported = $progress;
                $onProgress($progress, $stepLabel);
            }
        });

        if (! $result->successful()) {
            Log::error('ffmpeg failed', [
                'step' => $stepLabel,
                'command' => implode(' ', $command),
                'output' => $result->errorOutput(),
            ]);

            throw new RuntimeException("ffmpeg failed while creating {$stepLabel}");
        }

        if ($onProgress) {
            $onProgress($rangeEnd, $stepLabel);
        }
    }

    private function runFfmpeg(array $command, string $label): void
    {
        $result = Process::timeout(3600)->run($command);

        if (! $result->successful()) {
            Log::error("ffmpeg {$label} failed", [
                'command' => implode(' ', $command),
                'output' => $result->errorOutput(),
            ]);

            throw new RuntimeException("ffmpeg failed while creating {$label}");
        }
    }
}
