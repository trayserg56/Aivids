<?php

namespace Tests\Unit;

use App\Models\Video;
use App\Services\VideoConverter;
use App\Services\VideoUploadProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class VideoUploadProcessorTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_conversion_updates_video_record(): void
    {
        $video = Video::factory()->create([
            'slug' => 'test-case',
            'poster_path' => 'posters/old.webp',
        ]);

        $converter = Mockery::mock(VideoConverter::class);
        $converter->shouldReceive('convert')
            ->once()
            ->with('/tmp/source.mov', 'test-case', null)
            ->andReturn([
                'video_path' => 'videos/test-case.mp4',
                'preview_path' => 'videos/test-case-preview.mp4',
                'poster_path' => 'posters/test-case.webp',
                'file_size_bytes' => 1024,
                'width' => 1920,
                'height' => 1080,
            ]);

        $processor = new VideoUploadProcessor($converter);
        $processor->applyConversion($video, '/tmp/source.mov', 'source.mov');

        $video->refresh();

        $this->assertSame('videos/test-case.mp4', $video->video_path);
        $this->assertSame('videos/test-case-preview.mp4', $video->preview_path);
        $this->assertSame('posters/test-case.webp', $video->poster_path);
        $this->assertSame('source.mov', $video->source_filename);
        $this->assertSame(1920, $video->width);
    }
}
