<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\VideoConverter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProbeVideoDimensionsCommand extends Command
{
    protected $signature = 'videos:probe-dimensions';

    protected $description = 'Detect width/height for imported videos via ffprobe';

    public function handle(VideoConverter $converter): int
    {
        $videos = Video::query()->whereNotNull('video_path')->get();
        $updated = 0;

        foreach ($videos as $video) {
            $path = Storage::disk('public')->path($video->video_path);

            if (! is_file($path)) {
                $this->warn("Missing file: {$video->video_path}");
                continue;
            }

            [$width, $height] = $converter->probeDimensions($path);

            $video->update(['width' => $width, 'height' => $height]);
            $updated++;
            $this->line("  {$video->slug}: {$width}x{$height}");
        }

        Cache::forget('home.page');
        $this->info("Updated {$updated} videos.");

        return self::SUCCESS;
    }
}
