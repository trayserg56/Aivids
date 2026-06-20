<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\VideoConverter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ImportVideosCommand extends Command
{
    protected $signature = 'videos:import
                            {--path= : Source directory with raw videos}
                            {--fresh : Remove existing portfolio videos before import}
                            {--featured=8 : Number of videos to mark as featured}';

    protected $description = 'Convert raw videos from source folder and import into portfolio';

    public function handle(VideoConverter $converter): int
    {
        $sourcePath = $this->option('path') ?: config('aivids.videos_source_path');

        if (! is_dir($sourcePath)) {
            $this->error("Source directory not found: {$sourcePath}");

            return self::FAILURE;
        }

        $files = collect(File::files($sourcePath))
            ->filter(fn ($file) => $converter->isVideoFile($file->getPathname()))
            ->sortBy(fn ($file) => $file->getFilename())
            ->values();

        if ($files->isEmpty()) {
            $this->warn('No video files found.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            Video::query()->whereNotNull('source_filename')->delete();
            Video::query()->where('slug', 'like', 'demo-%')->delete();
            $this->info('Removed previous imported/demo videos.');
        }

        $featuredCount = (int) $this->option('featured');
        $imported = 0;

        foreach ($files as $index => $file) {
            $filename = $file->getFilename();
            $slug = $converter->slugFromFilename($filename);

            if ($slug === '') {
                $this->warn("Skipping {$filename}: unable to build slug.");
                continue;
            }

            $this->info(sprintf('Converting [%d/%d] %s...', $index + 1, $files->count(), $filename));

            try {
                $paths = $converter->convert($file->getPathname(), $slug);

                Video::query()->updateOrCreate(
                    ['source_filename' => $filename],
                    [
                        'title' => $converter->titleFromFilename($filename),
                        'slug' => $slug,
                        'description' => null,
                        'poster_path' => $paths['poster_path'],
                        'width' => $paths['width'],
                        'height' => $paths['height'],
                        'video_path' => $paths['video_path'],
                        'preview_path' => $paths['preview_path'],
                        'file_size_bytes' => $paths['file_size_bytes'],
                        'categories' => ['Портфолио'],
                        'sort_order' => $index,
                        'is_featured' => $index < $featuredCount,
                        'is_published' => true,
                    ],
                );

                $imported++;
                $sizeMb = round($paths['file_size_bytes'] / 1024 / 1024, 1);
                $this->line("  ✓ {$slug}.mp4 ({$sizeMb} MB)");
            } catch (\Throwable $e) {
                $this->error("  ✗ Failed: {$e->getMessage()}");
            }
        }

        Cache::forget('home.page');
        $this->newLine();
        $this->info("Imported {$imported} of {$files->count()} videos.");

        return self::SUCCESS;
    }
}
