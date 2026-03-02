#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-10000}"

if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY is missing. Generate one with: php artisan key:generate --show"
  exit 1
fi

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan storage:link || true
php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="$PORT"
