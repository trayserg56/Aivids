<?php

namespace App\Http\Controllers;

use App\Services\VideoCatalog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CaseController extends Controller
{
    public function __invoke(Request $request, VideoCatalog $catalog): Response
    {
        $videos = $catalog->publishedPayload($request);

        $categories = collect($videos)
            ->flatMap(fn (array $video) => $video['categories'] ?? [])
            ->unique()
            ->sort()
            ->values()
            ->all();

        return Inertia::render('Cases/Index', [
            'videos' => $videos,
            'categories' => $categories,
        ]);
    }
}
