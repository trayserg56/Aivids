<?php

namespace App\Support;

class ContactSources
{
    /** @var array<string, string> */
    public const SECTION_LABELS = [
        'hero' => 'Первый экран',
        'header' => 'Шапка сайта',
        'footer' => 'Подвал',
        'pricing' => 'Стоимость',
        'cases' => 'Кейсы',
        'contact' => 'Контакты',
    ];

    public static function sectionLabel(?string $section): ?string
    {
        if ($section === null) {
            return null;
        }

        return self::SECTION_LABELS[$section] ?? $section;
    }
}
