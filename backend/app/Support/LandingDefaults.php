<?php

namespace App\Support;

class LandingDefaults
{
    /** @return array<string, string> */
    public static function hero(): array
    {
        return [
            'badge' => 'Видео от 15 000 ₽ — без съёмок и сложного продакшна',
            'title' => 'ИИ-видео под ключ — масштабный визуал без съёмок',
            'subtitle' => 'Создаём AI-видео для компаний, брендов и шоу-проектов — от корпоративных историй до концертного визуала',
            'cta' => 'Обсудить проект',
        ];
    }
}
