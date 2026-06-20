#!/usr/bin/env bash
# Download videos from a public Yandex Disk folder and import into AiVids.
set -euo pipefail

PUBLIC_URL="${1:-https://disk.yandex.ru/d/yLZjqKx2st_7VQ}"
APP_DIR="${APP_DIR:-/var/www/aivids}"
DEST="${APP_DIR}/Videos"
LOG="${APP_DIR}/backend/storage/logs/yandex-import.log"

mkdir -p "$(dirname "${LOG}")" "${DEST}"
exec > >(tee -a "${LOG}") 2>&1

echo "==> $(date -Is) Download from Yandex Disk: ${PUBLIC_URL}"

ENCODED_URL=$(python3 -c "import urllib.parse; print(urllib.parse.quote('${PUBLIC_URL}'))")

mapfile -t FILES < <(curl -fsS \
  "https://cloud-api.yandex.net/v1/disk/public/resources?public_key=${ENCODED_URL}&limit=100" \
  | python3 -c "
import json, sys
data = json.load(sys.stdin)
for item in data.get('_embedded', {}).get('items', []):
    if item.get('type') == 'file':
        print(item['path'])
")

if [[ ${#FILES[@]} -eq 0 ]]; then
  echo "ERROR: No files found in folder"
  exit 1
fi

echo "Found ${#FILES[@]} files"

for path in "${FILES[@]}"; do
  name=$(basename "${path}")
  dest="${DEST}/${name}"

  if [[ -f "${dest}" ]]; then
    echo "Skip (exists): ${name}"
    continue
  fi

  echo "Downloading: ${name}"
  ENCODED_PATH=$(python3 -c "import urllib.parse; print(urllib.parse.quote('${path}'))")
  HREF=$(curl -fsS \
    "https://cloud-api.yandex.net/v1/disk/public/resources/download?public_key=${ENCODED_URL}&path=${ENCODED_PATH}" \
    | python3 -c "import json,sys; print(json.load(sys.stdin)['href'])")

  curl -fsSL --retry 3 --retry-delay 5 -o "${dest}" "${HREF}"
  echo "  ✓ $(du -h "${dest}" | cut -f1)"
done

echo "==> $(date -Is) Import videos"
cd "${APP_DIR}"
set -a
source .env
set +a

docker compose exec -T app php artisan videos:import --fresh

echo "==> $(date -Is) Done"
