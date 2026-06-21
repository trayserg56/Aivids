#!/usr/bin/env bash
# Align /var/www/aivids git checkout with origin/main after rsync deploys.
# Routine deploy uses rsync (see .github/workflows/deploy.yml), not git pull —
# this script fixes "local changes / untracked would be overwritten" on the server.
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/aivids}"
cd "${APP_DIR}"

if [[ ! -d .git ]]; then
  echo "ERROR: ${APP_DIR} is not a git repository."
  exit 1
fi

git -c safe.directory="${APP_DIR}" fetch origin main

echo "==> Remove stray untracked files (keep .env, storage, vendor, Videos)"
git -c safe.directory="${APP_DIR}" clean -fd \
  -e .env \
  -e backend/.env \
  -e backend/storage \
  -e backend/vendor \
  -e backend/public/storage \
  -e Videos

echo "==> Reset tracked files to origin/main"
git -c safe.directory="${APP_DIR}" reset --hard origin/main

echo "==> Done: $(git -c safe.directory="${APP_DIR}" rev-parse --short HEAD) ($(git -c safe.directory="${APP_DIR}" log -1 --format='%s'))"
