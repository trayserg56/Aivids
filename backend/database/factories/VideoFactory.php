<?php

namespace Database\Factories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** @extends Factory<Video> */
class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition(): array
    {
        $posterPath = 'posters/'.Str::uuid().'.svg';

        Storage::disk('public')->put($posterPath, '<svg xmlns="http://www.w3.org/2000/svg" width="640" height="360"><rect width="640" height="360" fill="#2563eb"/></svg>');

        return [
            'title' => fake()->sentence(3),
            'slug' => Str::slug(fake()->unique()->sentence(3)),
            'description' => fake()->sentence(),
            'poster_path' => $posterPath,
            'video_path' => 'videos/'.Str::uuid().'.mp4',
            'categories' => [fake()->randomElement(['Реклама', 'Бизнес', 'Клипы'])],
            'sort_order' => fake()->numberBetween(0, 100),
            'is_featured' => false,
            'is_published' => true,
            'conversion_status' => 'completed',
            'conversion_progress' => 100,
        ];
    }
}
