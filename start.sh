#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-10000}"

if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY is missing. Generate one with: php artisan key:generate --show"
  exit 1
fi

run_step() {
  echo "==> $1"
  shift
  "$@"
}

run_step "Clearing config cache" php artisan config:clear
run_step "Clearing route cache" php artisan route:clear
run_step "Clearing compiled views" php artisan view:clear
run_step "Clearing application cache" php artisan cache:clear

run_step "Creating storage symlink (safe if exists)" php artisan storage:link || true
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  run_step "Running migrations" php artisan migrate --force
else
  echo "==> Skipping migrations (set RUN_MIGRATIONS=true to enable)"
fi
run_step "Caching config" php artisan config:cache
run_step "Caching routes" php artisan route:cache
run_step "Caching views" php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="$PORT"
