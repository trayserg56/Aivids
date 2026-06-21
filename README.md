# AiVids — ИИ-видео под ключ

Лендинг и CMS для услуги по созданию AI-роликов. Дизайн-референс: [neurofilms.ru](https://neurofilms.ru/).

**Репозиторий:** https://github.com/trayserg56/Aivids  
**Прод:** https://adsaivideo.ru

## Стек

| Слой | Технологии |
|------|------------|
| Frontend | Vue 3, Inertia.js, Tailwind CSS 4, Vite |
| Backend | Laravel 13, PHP 8.4 |
| Admin | Filament 4 |
| DB / Cache / Queue | PostgreSQL 16, Redis 7 |
| Infra | Docker Compose |

## Быстрый старт (локально)

```bash
docker compose up -d
docker exec aivids_app php artisan migrate --seed
docker exec aivids_app npm install --legacy-peer-deps && npm run build
```

- **Сайт:** http://localhost:8091
- **Админка:** http://localhost:8091/admin (`admin@aivids.local` / `password`) — **на проде смените пароль**
- **Vite dev:** `docker compose --profile dev up node` → порт **5175**

Очередь (Telegram, конвертация видео из админки):

```bash
docker compose up -d queue
# или вручную:
docker exec aivids_queue php artisan queue:work redis --sleep=3 --tries=1 --timeout=3700
```

## Порты

| Окружение | Web | Vite |
|-----------|-----|------|
| Локально (Mac) | **8091** | **5175** |
| Прод (Docker на VPS) | **8092** → nginx-proxy | — |

Порты 8091/5175 выбраны, чтобы не конфликтовать с другими проектами (8080, 8088, 8090, 5173, 5174). Postgres и Redis — только внутри docker-сети.

## Структура проекта

```
AiVids/
├── backend/           # Laravel + Vue (Inertia)
├── docker/            # PHP, Nginx, Node configs
├── deploy/            # deploy.sh, CI/CD, nginx prod
├── Videos/            # исходники для импорта (локально, не в git)
├── .cursor/skills/    # Project skills для Cursor
├── docker-compose.yml
├── docker-compose.prod.yml
└── README.md
```

## Разделы лендинга

1. **Hero** — заголовок и CTA «Обсудить проект»
2. **Featured-галерея** — сразу под hero: **8 последних** обновлённых роликов (`orderByDesc('updated_at')`)
3. Услуги (карусель)
4. Статистика
5. **Блок «Кейсы» на главной** — превью 2 рядов + градиент + кнопка «Все кейсы →»
6. Преимущества
7. Стоимость
8. Новости (превью → `/news`)
9. FAQ
10. Footer / контакты

Отдельная страница **`/cases`** — все опубликованные ролики с фильтром по категориям.

## Галерея видео (justified layout)

Сетка по мотивам Flickr/Google Photos: **фиксированная высота ряда**, **ширина плитки = пропорции ролика** (`width` / `height` из ffprobe). Вертикальные — узкие, горизонтальные — широкие, квадратные — квадратные.

| Место | Компонент | Поведение |
|-------|-----------|-----------|
| Под hero | `JustifiedVideoStrip` | 8 последних роликов, полная ширина, `row-height=220` |
| Блок на главной | `VideoGallery` → `target-rows=2` | Пул 20 последних; превью 2 рядов; лимит растяжения ~12% |
| `/cases` | `JustifiedVideoStrip` | Все ролики, ряды на всю ширину |

Логика: `backend/resources/js/composables/useJustifiedGallery.js`  
Тесты: `backend/resources/js/__tests__/useJustifiedGallery.test.js`

**Размеры роликов** хранятся в `videos.width` / `videos.height`. После импорта или если пропорции «плывут»:

```bash
docker exec aivids_app php artisan videos:probe-dimensions
docker exec aivids_app php artisan cache:clear   # сброс кеша главной
```

`probeDimensions` учитывает rotation 90°/270° в метаданных ffprobe.

## Видео: импорт, конвертация, админка

### Импорт из папки `Videos/`

```bash
docker exec aivids_app php artisan videos:import
docker exec aivids_app php artisan videos:import --fresh   # удалить старые и импортировать заново
```

ffmpeg в контейнере создаёт:
- **Основное MP4** — до 1920px по ширине, H.264, preset `medium`
- **Preview** — `{slug}-preview.mp4`, до 854px, без звука (hover на сайте)
- **Постер** — WebP из кадра на 1-й секунде

Папка `Videos/` смонтирована в контейнер как read-only (`VIDEOS_SOURCE_PATH`).

### Загрузка через Filament

`/admin` → Видео → загрузка файла → **очередь** `ConvertVideoJob` (прогресс в админке).  
Без работающего `queue`-контейнера конвертация и Telegram не выполняются.

## Форма «Обсудить проект»

- Модальное окно с полями + **Yandex SmartCaptcha** (invisible, `hideShield: true`)
- Rate limit: 6 заявок / мин с IP
- Заявки: `/admin` → **Заявки**
- После успеха — отдельный popup (`ContactSuccessPopup`), автозакрытие ~5 с

### Yandex SmartCaptcha

В `backend/.env`:

```env
YANDEX_SMARTCAPTCHA_CLIENT_KEY=ysc1_...
YANDEX_SMARTCAPTCHA_SERVER_KEY=ysc2_...
```

Добавьте **`adsaivideo.ru`** (и при необходимости `www.adsaivideo.ru`) в список разрешённых сайтов в консоли Yandex SmartCaptcha. Без ключей капча отключена (удобно для локальной разработки).

Invisible-режим: кнопки «Я не робот» нет; challenge — только при подозрительном запросе. Ссылка на политику Yandex — в тексте формы.

## Telegram-уведомления о заявках

В `backend/.env`:

```env
TELEGRAM_BOT_TOKEN=123456:ABC...
TELEGRAM_CHAT_ID=-100xxxxxxxxxx
```

1. Создай бота через [@BotFather](https://t.me/BotFather), добавь в группу (лучше **админом**).
2. Напиши в группе любое сообщение.
3. Узнай `chat_id`: `docker exec aivids_app php artisan telegram:discover-chats`
4. Пропиши `TELEGRAM_CHAT_ID`, на проде после смены `.env`:

```bash
docker compose exec app php artisan config:cache
docker compose exec app php artisan queue:restart
docker compose restart queue
```

Без обоих ключей уведомления отключены — форма работает как раньше.

> **Важно:** после `config:cache` на деплое worker нужно перезапускать — иначе jobs (Telegram, конвертация) могут выполняться со старым конфигом и «молча» не отправлять сообщения.

## Деплой (CI/CD)

Push в `main` → GitHub Actions: `npm test` + `npm run build` → rsync на VPS → `deploy/deploy.sh`.

| Параметр | Значение |
|----------|----------|
| Домен | **adsaivideo.ru** |
| Сервер | `root@5.253.188.165` |
| Путь | `/var/www/aivids` |

### Смена домена (миграция)

После настройки DNS (A-запись `adsaivideo.ru` → IP сервера):

```bash
ssh root@5.253.188.165
bash /var/www/aivids/deploy/migrate-domain.sh
```

Скрипт обновит `APP_URL`, nginx-proxy (timetoeat), выпустит SSL-сертификат, пересоберёт config cache.

Старый домен `aivids.saittikhonova.ru` можно оставить с редиректом или отключить отдельно в nginx.

Настройка секретов CI: [deploy/GITHUB_ACTIONS.md](deploy/GITHUB_ACTIONS.md)

`deploy/deploy.sh` после каждого деплоя:
- `docker compose up -d --build`
- **restart nginx** (актуальный IP контейнера `app` — в nginx `resolver 127.0.0.11` + `fastcgi_pass` через переменную, иначе 502 после rebuild)
- migrate, `config:cache`, `queue:restart`, **restart queue**

**Не деплоится:** `backend/.env`, корневой `.env`, `Videos/`, `backend/storage/` (медиа на сервере).

### Полезные команды на проде

```bash
cd /var/www/aivids
docker compose -f docker-compose.yml -f docker-compose.prod.yml ps
docker compose exec app php artisan migrate --force
docker compose exec app php artisan videos:probe-dimensions
docker compose exec app php artisan cache:clear
docker compose logs -f queue
```

## Производительность

- Redis-кеш главной (5 мин), сброс при изменении контента
- Lazy-load постеров + Intersection Observer
- Hover-preview: лёгкий `-preview.mp4`, `preload="none"`
- Nginx gzip + cache headers для статики
- Vite code splitting

### PHP-FPM (воркеры)

Конфиг: `docker/php/zz-aivids-pool.conf` (копируется в образ при build).

| Параметр | Значение | Зачем |
|----------|----------|-------|
| `pm.max_children` | **12** | Одновременных Laravel-запросов (на VPS 4 CPU / 8 GB RAM) |
| `pm.start_servers` | 4 | Прогретые воркеры после старта |
| `pm.max_requests` | 500 | Перезапуск воркера против утечек памяти |

После изменения — **rebuild** `app` и `queue`:

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml build app queue
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d app queue nginx
```

На 8 GB VPS с другими сервисами (timetoeat, mtproto и т.д.) **12** — разумный потолок.  
Не поднимай `max_children` без расчёта: `memory_limit` PHP = **512M** × N воркеров.

### Нагрузочное тестирование

**Снаружи** (с Mac/CI) цифры занижены из‑за TLS и RTT до VPS — это нормально.

**На сервере** (реальная пропускная способность PHP):

```bash
ssh root@5.253.188.165
docker exec aivids_nginx sh -c "apk add --no-cache apache2-utils 2>/dev/null; ab -n 500 -c 50 -q http://127.0.0.1/"
```

Замеры (2026-06, после `max_children=12`):

| Сценарий | RPS | p95 | Ошибки |
|----------|-----|-----|--------|
| Главная (localhost в VPS, кеш) | **~260** | ~220 ms @ c=50 | 0 |
| Главная @ c=200 | **~274** | ~760 ms | 0 |
| С Mac → HTTPS | ~7–14 | ~1.5 s | 0 (сеть) |

**Оценка для лендинга:** сервер держит **~250 HTML-страниц/с** (кеш прогрет).  
Это **сотни одновременных читателей** и **десятки тысяч визитов в день** без проблем.  
Узкое место при всплеске — очередь к PHP-FPM; статика (постеры, JS) — через nginx, тысячи req/s.

Hover-preview MP4 — отдельная нагрузка на канал и диск, не учитывается в `ab` по HTML.

**Доступ на сервер:** GitHub Actions деплоит по SSH-ключу (`deploy/GITHUB_ACTIONS.md`).  
Для ручного входа — `ssh root@5.253.188.165` (пароль только в панели хостинга, **не хранить в git**).

## Тесты

```bash
# PHP
docker exec aivids_app php artisan test

# Vue (Vitest)
docker run --rm -v $(pwd)/backend:/app -w /app node:22-alpine npm test
```

## Cursor Skills

В `.cursor/skills/` — скилы проекта (использовать при разработке):

- `aivids-design` — дизайн-система (референс neurofilms)
- `aivids-vue` — Vue/Inertia фронтенд
- `aivids-laravel` — бэкенд и Filament
- `aivids-devops` — Docker, порты, деплой

## Безопасность (TODO)

- [ ] **Сменить root-пароль VPS** после передачи в чат / панель
- [ ] Сменить пароль admin после первого входа на прод
- [ ] Ограничить `User::canAccessPanel()` (сейчас доступ открыт всем пользователям БД)
- [ ] Deploy-ключ вместо root SSH (см. `deploy/GITHUB_ACTIONS.md`)

---

## Идеи (backlog)

- [x] Форма «Обсудить проект» (заявки в админке `/admin` → Заявки)
- [x] Yandex SmartCaptcha + Telegram при новой заявке
- [x] Lightbox для видео (`VideoLightbox`, клик по карточке)
- [x] Конвертация видео через ffmpeg (`videos:import`, очередь из Filament)
- [x] Страница `/cases` с фильтром по категориям
- [x] Justified-галерея по пропорциям ролика
- [ ] CDN (Cloudflare R2 / S3) для видео
- [ ] A/B тест CTA-кнопок
- [ ] Яндекс.Метрика / GA4
- [ ] Multilingual (RU/EN)

## Changelog

### 2026-06-20 — PHP-FPM и нагрузочные тесты

- `pm.max_children` 5 → **12** (`docker/php/zz-aivids-pool.conf`)
- Прод: ~260 RPS главная (тест `ab` изнутри VPS), 0 ошибок до c=200

### 2026-06-20 — Галерея, деплой, уведомления

- Justified-галерея: ширина плитки по `width`/`height`, разные режимы для hero / превью / cases
- Превью на главной: 2 ряда, пул 20 роликов, лимит растяжения, раздельный подбор portrait/landscape
- `videos:probe-dimensions` + учёт rotation в ffprobe
- Страница `/cases`, `VideoCatalog`, lightbox по клику
- Telegram-бот: `telegram:discover-chats`, job на заявку, **queue:restart после deploy**
- SmartCaptcha invisible, success-popup отдельно от формы
- Fix nginx 502: Docker DNS resolver для PHP upstream
- Fix регрессий галереи: full-width для featured/cases, без `space-between`-дыр в превью

### 2026-06-20 — Форма заявок и импорт видео

- Форма «Обсудить проект» (Inertia + валидация + rate limit)
- Заявки в Filament: `/admin` → Заявки
- ffmpeg в Docker: MP4 + preview 480p + постер WebP
- Команда `videos:import` — импорт из папки `Videos/`
- Hover-preview использует `-preview.mp4`

### 2026-06-20 — Инициализация проекта

- Docker Compose (PHP 8.4, Nginx, Postgres, Redis) на портах 8091/5175
- Laravel 13 + Filament 4 + Inertia + Vue 3 + Tailwind 4
- Модели контента: Video, Service, Stat, NewsPost, PricingPlan, Faq
- Лендинг по мотивам neurofilms.ru (тёмная тема, синий акцент)
- Redis-кеш главной + observer сброса кеша
- PHPUnit + Vitest, project skills в `.cursor/skills/`
