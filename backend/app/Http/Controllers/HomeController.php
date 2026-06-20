<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use App\Models\Faq;
use App\Models\NewsPost;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Stat;
use App\Support\LandingDefaults;
use App\Services\VideoCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(Request $request, VideoCatalog $catalog): Response
    {
        $data = Cache::remember('home.page', 300, fn () => $this->buildPageData($request, $catalog));

        return Inertia::render('Home', $data);
    }

    private function buildPageData(Request $request, VideoCatalog $catalog): array
    {
        $latestVideos = fn (int $limit) => $catalog->publishedPayload(
            $request,
            $limit,
            fn ($query) => $query->reorder()->orderByDesc('updated_at'),
        );

        return [
            'hero' => SiteSetting::get('hero', LandingDefaults::hero()),
            'services' => Service::published()
                ->get(['id', 'title', 'slug', 'description', 'image'])
                ->map(fn (Service $service) => [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'description' => $service->description,
                    'image' => $service->image,
                ])
                ->all(),
            'statsSection' => SiteSetting::get('stats', [
                'title' => 'Опыт в производстве ИИ-видео',
            ]),
            'stats' => Stat::query()
                ->orderBy('sort_order')
                ->get(['value', 'label', 'is_highlighted'])
                ->map(fn (Stat $stat) => [
                    'value' => $stat->value,
                    'label' => $stat->label,
                    'is_highlighted' => $stat->is_highlighted,
                ])
                ->all(),
            'featuredVideos' => $latestVideos(8),
            'videos' => $latestVideos(6),
            'benefitsSection' => SiteSetting::get('benefits', [
                'title' => 'Преимущества ИИ видеопроизводства',
                'subtitle' => 'Видео создаётся без студии, актёров и сложной организации съёмок.',
            ]),
            'benefits' => Benefit::query()
                ->orderBy('sort_order')
                ->get(['id', 'title', 'text'])
                ->map(fn (Benefit $benefit) => [
                    'id' => $benefit->id,
                    'title' => $benefit->title,
                    'text' => $benefit->text,
                ])
                ->all(),
            'pricingSection' => SiteSetting::get('pricing', [
                'title' => 'Стоимость ИИ-видео',
                'footer' => 'Стоимость зависит от сложности проекта и длительности видео',
            ]),
            'pricing' => PricingPlan::query()
                ->orderBy('sort_order')
                ->get(['id', 'name', 'price_label', 'description', 'features', 'is_recommended'])
                ->map(fn (PricingPlan $plan) => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price_label' => $plan->price_label,
                    'description' => $plan->description,
                    'features' => $plan->features ?? [],
                    'is_recommended' => $plan->is_recommended,
                ])
                ->all(),
            'faqSection' => SiteSetting::get('faq', [
                'title' => 'Часто задаваемые вопросы',
            ]),
            'faqs' => Faq::query()
                ->orderBy('sort_order')
                ->get(['id', 'question', 'answer'])
                ->map(fn (Faq $faq) => [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ])
                ->all(),
            'latestNews' => NewsPost::published()->limit(3)->get()->map(fn (NewsPost $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'category' => $post->category,
                'published_at' => $post->published_at?->translatedFormat('d F Y'),
                'image_url' => $post->image_url,
            ])->all(),
        ];
    }
}
