# GitHub Actions — автодеплой на прод

При push в `main`:

1. **CI** — `npm test` + `npm run build` (фронт собирается на GitHub, не на VPS)
2. **CD** — rsync кода + `public/build/` на сервер → `deploy/deploy.sh` (composer, migrate, cache)

## Секреты репозитория

Settings → Secrets and variables → Actions → **New repository secret**

| Secret | Значение |
|--------|----------|
| `SSH_PRIVATE_KEY` | Приватный SSH-ключ (см. ниже) |
| `SERVER_HOST` | `5.253.188.165` |
| `SERVER_USER` | `root` (опционально, по умолчанию root) |
| `DEPLOY_PATH` | `/var/www/aivids` (опционально) |

### Создать deploy-ключ (один раз)

На своём Mac:

```bash
ssh-keygen -t ed25519 -f ~/.ssh/aivids_deploy -N "" -C "github-actions-aivids"
cat ~/.ssh/aivids_deploy.pub
```

Публичный ключ добавьте на сервер:

```bash
ssh root@5.253.188.165 "mkdir -p ~/.ssh && chmod 700 ~/.ssh && echo 'ВСТАВЬТЕ_ПУБЛИЧНЫЙ_КЛЮЧ' >> ~/.ssh/authorized_keys"
```

Приватный ключ — в GitHub secret `SSH_PRIVATE_KEY` (весь файл `~/.ssh/aivids_deploy`).

Через CLI:

```bash
gh secret set SSH_PRIVATE_KEY < ~/.ssh/aivids_deploy
gh secret set SERVER_HOST --body "5.253.188.165"
```

## Ручной запуск

Actions → Deploy → **Run workflow**

## Что не деплоится

- `backend/.env`, `.env` — только на сервере
- `Videos/` — загружаете сами
- `backend/storage/` — данные и загруженные медиа на сервере
- `node_modules`, `vendor` — ставятся на сервере через composer (vendor) / не нужны (node)
