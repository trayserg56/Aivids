<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function index(Request $request): Response
    {
        $posts = NewsPost::published()
            ->paginate(9)
            ->through(fn (NewsPost $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'category' => $post->category,
                'published_at' => $post->published_at?->translatedFormat('d F Y'),
                'image_url' => $post->image_url,
                'reading_time' => $post->reading_time,
            ]);

        return Inertia::render('News/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(string $slug): Response
    {
        $post = NewsPost::published()->where('slug', $slug)->firstOrFail();

        return Inertia::render('News/Show', [
            'post' => [
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'body' => $post->body,
                'category' => $post->category,
                'published_at' => $post->published_at?->translatedFormat('d F Y'),
                'image_url' => $post->image_url,
                'reading_time' => $post->reading_time,
            ],
            'related' => NewsPost::published()
                ->where('id', '!=', $post->id)
                ->limit(3)
                ->get(['title', 'slug', 'excerpt', 'category', 'published_at'])
                ->map(fn (NewsPost $item) => [
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'excerpt' => $item->excerpt,
                    'category' => $item->category,
                    'published_at' => $item->published_at?->translatedFormat('d F Y'),
                ]),
        ]);
    }
}
