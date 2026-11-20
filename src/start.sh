#!/bin/sh
set -e

echo "=== KULAAN.id Startup ==="

# Buat .env dari environment variables Railway (double quotes agar variabel ter-expand)
cat > .env <<EOF
APP_NAME=${APP_NAME:-Laravel}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-laravel}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}
SESSION_DRIVER=cookie
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
SANCTUM_STATEFUL_DOMAINS=${SANCTUM_STATEFUL_DOMAINS:-localhost}
EOF

echo "=== .env created ==="
cat .env
echo "=== Running artisan commands ==="

php artisan config:clear || true
php artisan storage:link 2>/dev/null || true
php artisan migrate --force 2>/dev/null || true

echo "=== Starting server on port ${PORT:-8080} ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
