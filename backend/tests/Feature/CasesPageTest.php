<?php

namespace Tests\Feature;

use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CasesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_cases_page_lists_videos_with_media(): void
    {
        Video::factory()->create([
            'title' => 'Visible Case',
            'video_path' => 'videos/test.mp4',
            'categories' => ['Клипы', 'Реклама'],
            'is_published' => true,
        ]);

        Video::factory()->create([
            'title' => 'Poster Only',
            'video_path' => null,
            'external_url' => null,
            'is_published' => true,
        ]);

        $response = $this->get('/cases');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Cases/Index')
            ->has('videos', 1)
            ->has('categories')
            ->where('videos.0.title', 'Visible Case')
        );
    }
}
