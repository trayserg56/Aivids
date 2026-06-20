<?php

namespace Tests\Feature;

use App\Models\NewsPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_index_lists_published_posts(): void
    {
        NewsPost::factory()->create([
            'title' => 'Published Post',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        NewsPost::factory()->create([
            'title' => 'Draft Post',
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get('/news');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('News/Index')
            ->has('posts.data', 1)
            ->where('posts.data.0.title', 'Published Post')
        );
    }

    public function test_news_show_displays_post_by_slug(): void
    {
        $post = NewsPost::factory()->create([
            'slug' => 'test-article',
            'title' => 'Test Article',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/news/test-article');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('News/Show')
            ->where('post.title', 'Test Article')
        );
    }

    public function test_unpublished_news_returns_404(): void
    {
        NewsPost::factory()->create([
            'slug' => 'secret',
            'is_published' => false,
        ]);

        $this->get('/news/secret')->assertNotFound();
    }
}
