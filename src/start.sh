#!/bin/sh

echo "=== KULAAN.id Startup ==="

TARGET_PORT="${PORT:-8080}"
echo "Configuring Apache to listen on port ${TARGET_PORT}..."
echo "Listen ${TARGET_PORT}" > /etc/apache2/ports.conf

# Matikan modul MPM ganda agar Apache tidak error AH00534
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Buat .env dari environment variables Railway (lengkap dengan fallback default)
cat > .env <<EOF
APP_NAME="${APP_NAME:-KULAAN.id}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-base64:jhD6ZSKg2fDrL3NHOsljkleoIy/pBhzct9TjCmj7hVo=}"
APP_DEBUG=${APP_DEBUG:-true}
APP_URL="${APP_URL:-https://praktikum-rpl-a-7-production.up.railway.app}"
ASSET_URL="${APP_URL:-https://praktikum-rpl-a-7-production.up.railway.app}"
DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-railway}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
SANCTUM_STATEFUL_DOMAINS="${SANCTUM_STATEFUL_DOMAINS:-praktikum-rpl-a-7-production.up.railway.app}"
EOF

echo "=== Preparing storage directories and clearing stale caches ==="
rm -f bootstrap/cache/*.php
rm -rf storage/framework/views/*.php
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chmod -R 777 storage bootstrap/cache

echo "=== Running artisan commands ==="
php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true
php artisan package:discover --ansi || true
php artisan storage:link || true
php artisan migrate --force || true

echo "=== Testing Apache syntax ==="
apache2ctl -t || true

echo "=== Starting Apache Web Server on port ${TARGET_PORT} ==="
exec apache2-foreground
