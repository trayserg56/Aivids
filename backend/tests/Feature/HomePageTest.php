<?php

namespace Tests\Feature;

use App\Models\NewsPost;
use App\Models\PricingPlan;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Home')
            ->has('hero')
            ->has('services')
            ->has('statsSection')
            ->has('stats')
            ->has('benefitsSection')
            ->has('benefits')
            ->has('pricingSection')
            ->has('faqSection')
            ->has('videos')
        );
    }

    public function test_home_page_includes_published_videos(): void
    {
        Video::factory()->create(['is_published' => true, 'title' => 'Visible Video']);
        Video::factory()->create(['is_published' => false, 'title' => 'Hidden Video']);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->has('videos', 1)
            ->where('videos.0.title', 'Visible Video')
        );
    }

    public function test_home_page_includes_pricing_plans_as_plain_arrays(): void
    {
        PricingPlan::query()->create([
            'name' => 'Базовый',
            'price_label' => 'от 15 000 ₽',
            'description' => 'Для простых проектов.',
            'features' => ['Монтаж'],
            'is_recommended' => false,
            'sort_order' => 0,
        ]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->has('pricing', 1)
            ->where('pricing.0.name', 'Базовый')
            ->where('pricing.0.price_label', 'от 15 000 ₽')
            ->where('pricing.0.features.0', 'Монтаж')
        );
    }
}
