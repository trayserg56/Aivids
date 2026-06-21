#!/usr/bin/env bash
# Switch production from aivids.saittikhonova.ru to adsaivideo.ru (run on VPS after DNS A-record).
set -euo pipefail

DOMAIN="${DOMAIN:-adsaivideo.ru}"
APP_DIR="${APP_DIR:-/var/www/aivids}"
TIMETOEAT_DIR="${TIMETOEAT_DIR:-/var/www/timetoeat}"
CERTBOT_EMAIL="${CERTBOT_EMAIL:-admin@saittikhonova.ru}"
NGINX_CONF="${TIMETOEAT_DIR}/docker/nginx/adsaivideo.conf"
COMPOSE_SSL="${TIMETOEAT_DIR}/compose.ssl.yaml"

echo "==> Migrate AiVids to https://${DOMAIN}"

cd "${APP_DIR}"
git pull --ff-only

if [[ -f backend/.env ]]; then
  if grep -q '^APP_URL=' backend/.env; then
    sed -i "s|^APP_URL=.*|APP_URL=https://${DOMAIN}|" backend/.env
  else
    echo "APP_URL=https://${DOMAIN}" >> backend/.env
  fi
  echo "Updated APP_URL in backend/.env"
fi

cp "${APP_DIR}/deploy/nginx/adsaivideo.http.conf" "${NGINX_CONF}"

if grep -q 'aivids-ai.conf' "${COMPOSE_SSL}" 2>/dev/null; then
  sed -i 's|aivids-ai.conf|adsaivideo.conf|g' "${COMPOSE_SSL}"
  echo "Updated compose.ssl.yaml mount to adsaivideo.conf"
elif ! grep -q 'adsaivideo.conf' "${COMPOSE_SSL}" 2>/dev/null; then
  sed -i '/active-ssl.conf/a\      - ./docker/nginx/adsaivideo.conf:/etc/nginx/conf.d/adsaivideo.conf:ro' \
    "${COMPOSE_SSL}"
  echo "Added adsaivideo.conf mount to compose.ssl.yaml"
fi

docker compose -f "${TIMETOEAT_DIR}/compose.yaml" \
  -f "${TIMETOEAT_DIR}/compose.prod.yaml" \
  -f "${COMPOSE_SSL}" \
  up -d nginx

docker exec timetoeat-nginx-1 nginx -t
docker exec timetoeat-nginx-1 nginx -s reload

if [[ ! -d "${TIMETOEAT_DIR}/certbot/conf/live/${DOMAIN}" ]]; then
  echo "==> Requesting Let's Encrypt certificate for ${DOMAIN} (+ www)"
  docker run --rm \
    -v "${TIMETOEAT_DIR}/certbot/conf:/etc/letsencrypt" \
    -v "${TIMETOEAT_DIR}/certbot/www:/var/www/certbot" \
    certbot/certbot certonly \
    --webroot -w /var/www/certbot \
    -d "${DOMAIN}" \
    -d "www.${DOMAIN}" \
    --email "${CERTBOT_EMAIL}" \
    --agree-tos \
    --no-eff-email \
    -n
fi

cp "${APP_DIR}/deploy/nginx/adsaivideo.ssl.conf" "${NGINX_CONF}"

docker exec timetoeat-nginx-1 nginx -t
docker exec timetoeat-nginx-1 nginx -s reload

cd "${APP_DIR}"
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -T app php artisan queue:restart
docker compose -f docker-compose.yml -f docker-compose.prod.yml restart queue nginx

echo "==> Done: https://${DOMAIN}"
echo "==> Add ${DOMAIN} to Yandex SmartCaptcha allowed hosts if captcha is enabled."
