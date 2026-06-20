<?php

namespace Tests\Unit;

use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_video_resource_includes_urls(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posters/test.svg', 'content');

        $video = Video::factory()->create([
            'poster_path' => 'posters/test.svg',
        ]);

        $data = (new VideoResource($video))->resolve();

        $this->assertSame('posters/test.svg', $video->poster_path);
        $this->assertStringContainsString('posters/test.svg', $data['poster_url']);
        $this->assertSame($video->title, $data['title']);
    }
}
