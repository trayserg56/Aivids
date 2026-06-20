<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class VideoUploadProcessor
{
    public function __construct(
        private readonly VideoConverter $converter,
    ) {}

    /** @param  callable(int $progress, string $step): void|null  $onProgress */
    public function applyConversion(
        Video $video,
        string $inputAbsolutePath,
        ?string $sourceFilename = null,
        ?callable $onProgress = null,
    ): void {
        $paths = $this->converter->convert($inputAbsolutePath, $video->slug, $onProgress);

        $video->update([
            'poster_path' => $paths['poster_path'],
            'video_path' => $paths['video_path'],
            'preview_path' => $paths['preview_path'],
            'width' => $paths['width'],
            'height' => $paths['height'],
            'file_size_bytes' => $paths['file_size_bytes'],
            'source_filename' => $sourceFilename,
        ]);
    }

    public function resolvePublicPath(string|array|null $uploaded): ?string
    {
        if ($uploaded === null || $uploaded === []) {
            return null;
        }

        $relative = is_array($uploaded) ? ($uploaded[0] ?? null) : $uploaded;

        if (! is_string($relative) || $relative === '') {
            return null;
        }

        return Storage::disk('public')->path($relative);
    }

    public function resolvePublicRelativePath(string|array|null $uploaded): ?string
    {
        if ($uploaded === null || $uploaded === []) {
            return null;
        }

        $relative = is_array($uploaded) ? ($uploaded[0] ?? null) : $uploaded;

        return is_string($relative) && $relative !== '' ? $relative : null;
    }

    public function deletePublicFile(string|array|null $uploaded): void
    {
        if ($uploaded === null || $uploaded === []) {
            return;
        }

        $relative = is_array($uploaded) ? ($uploaded[0] ?? null) : $uploaded;

        if (is_string($relative) && $relative !== '') {
            Storage::disk('public')->delete($relative);
        }
    }
}
