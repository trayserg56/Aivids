---
name: aivids-laravel
description: Laravel backend and Filament admin conventions for AiVids. Use when working with models, migrations, controllers, Filament resources, or PHPUnit tests.
---

# AiVids Laravel Backend

## Stack

- Laravel 13, PHP 8.4
- Filament 4 admin at `/admin`
- Inertia responses for public site
- PostgreSQL + Redis

## Content models

| Model | Purpose |
|-------|---------|
| `Service` | Landing services carousel |
| `Stat` | Metrics block |
| `Video` | Portfolio items (poster + optional video file/URL) |
| `NewsPost` | Blog/news |
| `PricingPlan` | Pricing tiers |
| `Faq` | FAQ accordion |

## Patterns

- Public controllers return `Inertia::render()` with cached homepage data
- `VideoResource` for consistent API serialization
- `ContentCacheObserver` clears `home.page` cache on content changes
- Scopes: `published()` on Video, NewsPost, Service

## Filament

Resources in `app/Filament/Resources/`. Video uploads:
- Poster → `storage/app/public/posters`
- Video → `storage/app/public/videos`

Run `php artisan storage:link` after deploy.

## Tests

```bash
docker exec aivids_app php artisan test
```

- Feature: `HomePageTest`, `NewsPageTest` (Inertia assertions)
- Unit: `VideoResourceTest`
- Factories: `VideoFactory`, `NewsPostFactory`
- PHPUnit uses SQLite in-memory

## Adding content type

1. Migration + Model + Factory
2. Filament Resource (`make:filament-resource`)
3. Register observer in `AppServiceProvider`
4. Add to `HomeController` cache payload if shown on landing
