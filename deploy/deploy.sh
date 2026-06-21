#!/usr/bin/env bash
# Production deploy (called from GitHub Actions). Frontend is built in CI — not on server.
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/aivids}"
cd "${APP_DIR}"

if [[ ! -f .env ]] || [[ ! -f backend/.env ]]; then
  echo "ERROR: Missing .env files. Run deploy/setup-server.sh once for initial setup."
  exit 1
fi

set -a
source .env
set +a

echo "==> Docker services"
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

echo "==> Reload nginx (pick up new app container IP)"
docker compose restart nginx

echo "==> Composer"
docker compose exec -T app composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Migrations"
docker compose exec -T app php artisan migrate --force --no-interaction

echo "==> Permissions"
docker compose exec -T app php artisan storage:link --force 2>/dev/null || true
docker compose exec -T app chown -R www-data:www-data storage bootstrap/cache
docker compose exec -T app chmod -R 775 storage bootstrap/cache

echo "==> Laravel cache"
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache
docker compose exec -T app php artisan filament:optimize

echo "==> Restart queue worker (pick up fresh config)"
docker compose exec -T app php artisan queue:restart
docker compose restart queue

if [[ -x deploy/align-git.sh ]]; then
  echo "==> Align git checkout with origin/main"
  bash deploy/align-git.sh || echo "WARN: git align skipped (non-fatal)"
fi

echo "==> Deploy complete: https://adsaivideo.ru"
