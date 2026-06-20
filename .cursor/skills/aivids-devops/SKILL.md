---
name: aivids-devops
description: Docker and deployment workflow for AiVids on Mac. Use when configuring containers, ports, migrations, CI, or local environment setup.
---

# AiVids DevOps

## Ports (avoid conflicts with other local projects)

| Service | Host port | Notes |
|---------|-----------|-------|
| Nginx (web) | **8091** | http://localhost:8091 |
| Vite HMR | **5175** | Dev only (`--profile dev`) |
| Postgres | internal | Not exposed — no conflict with 5432 |
| Redis | internal | Not exposed — no conflict with 6379 |

Occupied on this machine (do not use): 8080, 8088, 8090, 5173, 5174, 5432, 6379.

## Quick start

```bash
cd /path/to/AiVids
docker compose up -d              # app, nginx, postgres, redis
docker compose --profile dev up node   # Vite dev server
docker exec aivids_app php artisan migrate --seed
docker exec aivids_app npm run build
```

## Services

- `aivids_app` — PHP 8.4-FPM, Laravel 13
- `aivids_nginx` — static + gzip, caches assets 30d
- `aivids_postgres` — PostgreSQL 16
- `aivids_redis` — cache + sessions

## Common commands

```bash
docker exec aivids_app php artisan test
docker exec aivids_app php artisan filament:optimize
docker exec -it aivids_app php artisan tinker
docker compose logs -f app
```

## Admin panel

- URL: http://localhost:8091/admin
- Default seeder user: `admin@aivids.local` / `password`

## Performance stack

- Redis cache for homepage (`Cache::remember('home.page', 300, ...)`)
- Nginx gzip + long-cache headers for static assets
- Vite code splitting (`vendor` chunk)
- Content observers clear homepage cache on CRUD

## Production notes (future)

- CDN for `/storage` videos and posters
- Laravel Octane optional for higher concurrency
- `npm run build` in deploy pipeline; no Node in production runtime
