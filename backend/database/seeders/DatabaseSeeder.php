<?php

namespace Database\Seeders;

use App\Models\Benefit;
use App\Models\Faq;
use App\Models\NewsPost;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Stat;
use App\Models\User;
use App\Support\LandingDefaults;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@aivids.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ],
        );

        $services = [
            ['title' => 'ИИ-видео для бизнеса', 'description' => 'Рекламные и презентационные ролики для компаний, брендов и продуктов'],
            ['title' => 'Видеоконтент для мероприятий', 'description' => 'Видео для экранов конференций, форумов и корпоративных событий'],
            ['title' => 'Мультфильмы и анимационные ролики', 'description' => 'Анимационные истории и персонажи для брендов, медиа и блогеров'],
            ['title' => 'Рекламные ролики', 'description' => 'Видео для маркетинговых кампаний и продвижения брендов'],
            ['title' => 'Музыкальные клипы', 'description' => 'Клипы и визуальные проекты для артистов и музыкальных проектов'],
            ['title' => 'ИИ-аватары', 'description' => 'Цифровые ведущие и персонажи для обучающих видео и презентаций'],
            ['title' => 'Сценический визуал', 'description' => 'Генеративный видеоконтент для LED-экранов, концертов и шоу'],
        ];

        foreach ($services as $index => $service) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($service['title'])],
                [
                    ...$service,
                    'sort_order' => $index,
                    'is_published' => true,
                ],
            );
        }

        $stats = [
            ['value' => '130+', 'label' => 'видеопроектов разных форматов', 'is_highlighted' => false],
            ['value' => '15+', 'label' => 'лет опыта в видеопроизводстве', 'is_highlighted' => false],
            ['value' => '30+', 'label' => 'мероприятий с видеоконтентом для экранов', 'is_highlighted' => true],
            ['value' => '25+', 'label' => 'клиентов из бизнеса и шоу-индустрии', 'is_highlighted' => false],
        ];

        foreach ($stats as $index => $stat) {
            Stat::query()->updateOrCreate(
                ['label' => $stat['label']],
                [...$stat, 'sort_order' => $index],
            );
        }

        PricingPlan::query()->updateOrCreate(
            ['name' => 'Базовый'],
            [
                'price_label' => 'от 15 000 ₽',
                'description' => 'Для простых проектов.',
                'features' => ['Генерация видеосцен', 'Монтаж', 'Длительность от 10 секунд'],
                'is_recommended' => false,
                'sort_order' => 0,
            ],
        );

        PricingPlan::query()->updateOrCreate(
            ['name' => 'Стандартный ролик'],
            [
                'price_label' => 'от 25 000 ₽',
                'description' => 'Для рекламных и презентационных видео',
                'features' => ['Разработка сценария', 'Генерация нескольких сцен', 'Монтаж и графика'],
                'is_recommended' => true,
                'sort_order' => 1,
            ],
        );

        PricingPlan::query()->updateOrCreate(
            ['name' => 'Продакшн-проект'],
            [
                'price_label' => 'от 150 000 ₽',
                'description' => 'Для сложных проектов и клипов.',
                'features' => ['Разработка концепции', 'Сценарий', 'Генерация визуала', 'Монтаж и постпродакшн', 'Длительность до 90 секунд'],
                'is_recommended' => false,
                'sort_order' => 2,
            ],
        );

        $faqs = [
            ['question' => 'Где можно заказать ИИ видео?', 'answer' => 'Оставьте заявку на сайте или напишите нам в мессенджер — обсудим задачу и предложим формат.'],
            ['question' => 'Сколько стоит заказать ИИ-видео?', 'answer' => 'Базовые ролики от 15 000 ₽. Точная стоимость зависит от длительности, количества сцен и сложности визуала.'],
            ['question' => 'Сколько времени занимает создание AI-видео?', 'answer' => 'Простой ролик — от 3–5 рабочих дней. Сложные проекты обсуждаются индивидуально.'],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::query()->updateOrCreate(
                ['question' => $faq['question']],
                [...$faq, 'sort_order' => $index],
            );
        }

        SiteSetting::query()->updateOrCreate(
            ['key' => 'hero'],
            ['value' => LandingDefaults::hero()],
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'stats'],
            ['value' => ['title' => 'Опыт в производстве ИИ-видео']],
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'benefits'],
            ['value' => [
                'title' => 'Преимущества ИИ видеопроизводства',
                'subtitle' => 'Видео создаётся без студии, актёров и сложной организации съёмок.',
            ]],
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'pricing'],
            ['value' => [
                'title' => 'Стоимость ИИ-видео',
                'footer' => 'Стоимость зависит от сложности проекта и длительности видео',
            ]],
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'faq'],
            ['value' => ['title' => 'Часто задаваемые вопросы']],
        );

        $benefits = [
            ['title' => 'Видео без съёмочной логистики', 'text' => 'Запускайте рекламные ролики и презентации без аренды студии, актёров и сложных смен.'],
            ['title' => 'Быстрые версии под разные задачи', 'text' => 'Один сценарий адаптируется под сайт, соцсети, презентацию или рекламную кампанию.'],
            ['title' => 'Масштабный визуал без дорогих декораций', 'text' => 'Нейросети создают локации, персонажей и динамичные сцены без большого бюджета.'],
            ['title' => 'Гибкая доработка на каждом этапе', 'text' => 'Меняйте настроение, стиль, ракурсы и отдельные сцены уже в процессе работы.'],
            ['title' => 'Контент, который выглядит современно', 'text' => 'Визуальный язык, который считывается как технологичный и актуальный.'],
        ];

        foreach ($benefits as $index => $benefit) {
            Benefit::query()->updateOrCreate(
                ['title' => $benefit['title']],
                [...$benefit, 'sort_order' => $index],
            );
        }

        $this->seedNews();
    }

    private function seedNews(): void
    {
        $posts = [
            [
                'slug' => 'ai-video-for-business-2026',
                'image' => 'news/ai-business-2026.svg',
                'label' => 'AI × Business',
                'hue' => 220,
                'title' => 'ИИ-видео для бизнеса: как нейросети меняют корпоративный продакшн в 2026 году',
                'excerpt' => 'Современный бизнес переходит от PDF-презентаций к технологичному видеоконтенту.',
                'body' => '<p>ИИ-видео для бизнеса становится стандартом для презентаций, digital-кампаний и имиджевых запусков.</p><p>Компании получают масштабный визуал без съёмочной логистики и сокращают time-to-market.</p>',
                'published_at' => now()->subDays(3),
            ],
            [
                'slug' => 'ai-video-without-crew',
                'image' => 'news/ai-without-crew.svg',
                'label' => 'Production',
                'hue' => 280,
                'title' => 'AI-видео без съёмочной команды — как бизнес сокращает production-затраты',
                'excerpt' => 'Практические границы AI-подхода и сценарии, где выгоден гибридный продакшн.',
                'body' => '<p>Гибридный продакшн сочетает классический монтаж с генеративным визуалом там, где это даёт максимальный эффект.</p>',
                'published_at' => now()->subDays(10),
            ],
        ];

        foreach ($posts as $post) {
            Storage::disk('public')->put($post['image'], $this->newsPlaceholderSvg($post['label'], $post['hue']));

            NewsPost::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'body' => $post['body'],
                    'image' => $post['image'],
                    'category' => 'Бизнес',
                    'published_at' => $post['published_at'],
                    'is_published' => true,
                ],
            );
        }
    }

    private function newsPlaceholderSvg(string $label, int $hue): string
    {
        $accent = ($hue + 40) % 360;

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1280" height="800" viewBox="0 0 1280 800">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="hsl({$hue}, 65%, 22%)"/>
      <stop offset="100%" stop-color="hsl({$accent}, 70%, 12%)"/>
    </linearGradient>
    <radialGradient id="glow" cx="70%" cy="30%" r="50%">
      <stop offset="0%" stop-color="hsl({$hue}, 80%, 45%)" stop-opacity="0.55"/>
      <stop offset="100%" stop-color="hsl({$hue}, 80%, 45%)" stop-opacity="0"/>
    </radialGradient>
  </defs>
  <rect width="1280" height="800" fill="url(#bg)"/>
  <rect width="1280" height="800" fill="url(#glow)"/>
  <circle cx="320" cy="520" r="180" fill="hsl({$accent}, 60%, 35%)" opacity="0.35"/>
  <circle cx="980" cy="240" r="120" fill="hsl({$hue}, 70%, 50%)" opacity="0.25"/>
  <text x="80" y="720" fill="white" font-size="42" font-family="Inter, sans-serif" font-weight="700">{$label}</text>
  <text x="80" y="760" fill="white" fill-opacity="0.65" font-size="24" font-family="Inter, sans-serif">AiVids News</text>
</svg>
SVG;
    }
}
