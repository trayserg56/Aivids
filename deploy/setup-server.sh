#!/usr/bin/env bash
set -euo pipefail

DOMAIN="${DOMAIN:-ai.saittikhonova.ru}"
APP_DIR="${APP_DIR:-/var/www/aivids}"
TIMETOEAT_DIR="${TIMETOEAT_DIR:-/var/www/timetoeat}"
CERTBOT_EMAIL="${CERTBOT_EMAIL:-admin@saittikhonova.ru}"
REPO="${REPO:-https://github.com/trayserg56/Aivids.git}"

echo "==> Deploy AiVids to ${DOMAIN}"

if [[ ! -d "${APP_DIR}/.git" ]]; then
  git clone "${REPO}" "${APP_DIR}"
fi

cd "${APP_DIR}"
git pull --ff-only

if [[ ! -f .env ]]; then
  DB_PASSWORD="$(openssl rand -base64 24 | tr -dc 'A-Za-z0-9' | head -c 32)"
  APP_KEY="$(docker run --rm php:8.4-cli-alpine php -r "echo 'base64:'.base64_encode(random_bytes(32));")"

  cat > .env <<EOF
DB_PASSWORD=${DB_PASSWORD}
EOF

  cat > backend/.env <<EOF
APP_NAME=AiVids
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=https://${DOMAIN}

APP_LOCALE=ru
APP_FALLBACK_LOCALE=ru
APP_FAKER_LOCALE=ru_RU

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=aivids
DB_USERNAME=aivids
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=redis
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=redis

CACHE_STORE=redis

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@${DOMAIN}"
MAIL_FROM_NAME="\${APP_NAME}"

VIDEOS_SOURCE_PATH=/var/www/Videos
EOF
fi

set -a
source .env
set +a

docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

docker compose exec -T app chown -R www-data:www-data storage bootstrap/cache
docker compose exec -T app chmod -R 775 storage bootstrap/cache

docker compose exec -T app composer install --no-dev --optimize-autoloader --no-interaction
docker compose exec -T app php artisan migrate --force --no-interaction
docker compose exec -T app php artisan db:seed --force --no-interaction
docker compose exec -T app php artisan storage:link --force

docker run --rm \
  -v "${APP_DIR}/backend:/app" \
  -w /app \
  node:22-alpine \
  sh -c "npm ci --legacy-peer-deps && npm run build"

docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache
docker compose exec -T app php artisan filament:optimize

NGINX_CONF="${TIMETOEAT_DIR}/docker/nginx/aivids-ai.conf"
cp "${APP_DIR}/deploy/nginx/aivids-ai.http.conf" "${NGINX_CONF}"

if ! grep -q 'aivids-ai.conf' "${TIMETOEAT_DIR}/compose.ssl.yaml"; then
  sed -i '/active-ssl.conf/a\      - ./docker/nginx/aivids-ai.conf:/etc/nginx/conf.d/aivids-ai.conf:ro' \
    "${TIMETOEAT_DIR}/compose.ssl.yaml"
fi

docker compose -f "${TIMETOEAT_DIR}/compose.yaml" \
  -f "${TIMETOEAT_DIR}/compose.prod.yaml" \
  -f "${TIMETOEAT_DIR}/compose.ssl.yaml" \
  up -d nginx

docker exec timetoeat-nginx-1 nginx -t
docker exec timetoeat-nginx-1 nginx -s reload

if [[ ! -d "${TIMETOEAT_DIR}/certbot/conf/live/${DOMAIN}" ]]; then
  docker run --rm \
    -v "${TIMETOEAT_DIR}/certbot/conf:/etc/letsencrypt" \
    -v "${TIMETOEAT_DIR}/certbot/www:/var/www/certbot" \
    certbot/certbot certonly \
    --webroot -w /var/www/certbot \
    -d "${DOMAIN}" \
    --email "${CERTBOT_EMAIL}" \
    --agree-tos \
    --no-eff-email \
    -n || echo "WARN: SSL cert not issued yet (check DNS for ${DOMAIN})"
fi

if [[ -d "${TIMETOEAT_DIR}/certbot/conf/live/${DOMAIN}" ]]; then
  cp "${APP_DIR}/deploy/nginx/aivids-ai.ssl.conf" "${NGINX_CONF}"
  docker exec timetoeat-nginx-1 nginx -t
  docker exec timetoeat-nginx-1 nginx -s reload
fi

echo "==> Done. Site: https://${DOMAIN}"
echo "==> Admin: https://${DOMAIN}/admin (admin@aivids.local / password — change after login)"
