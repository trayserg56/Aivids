# AiVids — ИИ-видео под ключ

Лендинг и CMS для услуги по созданию AI-роликов. Дизайн-референс: [neurofilms.ru](https://neurofilms.ru/).

## Стек

| Слой | Технологии |
|------|------------|
| Frontend | Vue 3, Inertia.js, Tailwind CSS 4, Vite |
| Backend | Laravel 13, PHP 8.4 |
| Admin | Filament 4 |
| DB / Cache | PostgreSQL 16, Redis 7 |
| Infra | Docker Compose (Mac) |

## Быстрый старт

```bash
docker compose up -d
docker exec aivids_app php artisan migrate --seed
docker exec aivids_app npm install --legacy-peer-deps && npm run build
```

- **Сайт:** http://localhost:8091
- **Админка:** http://localhost:8091/admin (`admin@aivids.local` / `password`)
- **Vite dev:** `docker compose --profile dev up node` → порт **5175**

## Порты

Используем **8091** (web) и **5175** (Vite), чтобы не конфликтовать с другими проектами (8080, 8088, 8090, 5173, 5174).

## Структура проекта

```
AiVids/
├── backend/           # Laravel + Vue (Inertia)
├── docker/            # PHP, Nginx, Node configs
├── .cursor/skills/    # Project skills для Cursor
├── docker-compose.yml
└── README.md
```

## Разделы лендинга

1. Hero + сетка превью роликов
2. Услуги (карусель)
3. Статистика
4. Галерея видео (lazy-load, hover preview)
5. Преимущества
6. Стоимость
7. Новости (превью → `/news`)
8. FAQ
9. Footer / контакты

## Производительность

- Redis-кеш главной страницы (5 мин)
- Lazy loading постеров + Intersection Observer
- Видео загружается только при hover (`preload="none"`)
- Nginx gzip + cache headers для статики
- Vite code splitting

## Тесты

```bash
# PHP
docker exec aivids_app php artisan test

# Импорт боевых роликов из папки Videos/ (конвертация ffmpeg)
docker exec aivids_app php artisan videos:import --fresh

# Vue (Vitest)
docker run --rm -v $(pwd)/backend:/app -w /app node:22-alpine npm test
```

## Cursor Skills

В `.cursor/skills/` — скилы проекта (использовать при разработке):

- `aivids-design` — дизайн-система (референс neurofilms)
- `aivids-vue` — Vue/Inertia фронтенд
- `aivids-laravel` — бэкенд и Filament
- `aivids-devops` — Docker, порты, деплой

## Деплой (CI/CD)

Push в `main` → GitHub Actions собирает фронт и деплоит на https://aivids.saittikhonova.ru

Настройка секретов: [deploy/GITHUB_ACTIONS.md](deploy/GITHUB_ACTIONS.md)

---

## Идеи (backlog)

- [x] Форма «Обсудить проект» (заявки в админке `/admin` → Заявки)
- [ ] Lightbox для видео на весь экран
- [ ] CDN (Cloudflare R2 / S3) для видео — нужен внешний сервер/облако, локально не требуется
- [x] Конвертация видео через ffmpeg (`php artisan videos:import`)
- [ ] Страница «Кейсы» отдельно от галереи
- [ ] A/B тест CTA-кнопок
- [ ] Яндекс.Метрика / GA4
- [ ] Multilingual (RU/EN)

## Changelog

### 2026-06-20 — Форма заявок и импорт видео

- Форма «Обсудить проект» на лендинге (Inertia + валидация + rate limit)
- Заявки в Filament: `/admin` → Заявки
- ffmpeg в Docker: конвертация в MP4 (web) + preview 480p + постер WebP
- Команда `videos:import` — импорт из папки `Videos/`
- Hover-preview использует лёгкий `-preview.mp4`

### 2026-06-20 — Инициализация проекта

- Docker Compose (PHP 8.4, Nginx, Postgres, Redis) на портах 8091/5175
- Laravel 13 + Filament 4 + Inertia + Vue 3 + Tailwind 4
- Модели контента: Video, Service, Stat, NewsPost, PricingPlan, Faq
- Лендинг по мотивам neurofilms.ru (тёмная тема, синий акцент)
- Галерея видео с lazy-load и hover-preview
- Страницы новостей `/news`, `/news/{slug}`
- Filament CRUD для Video, Service, NewsPost
- Redis-кеш главной + observer сброса кеша
- PHPUnit + Vitest тесты
- Project skills в `.cursor/skills/`
