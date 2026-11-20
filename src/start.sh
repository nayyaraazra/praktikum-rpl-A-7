#!/bin/sh

echo "=== KULAAN.id Startup ==="

TARGET_PORT="${PORT:-8080}"
echo "Configuring Apache to listen on port ${TARGET_PORT}..."
echo "Listen ${TARGET_PORT}" > /etc/apache2/ports.conf

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
SESSION_DRIVER=cookie
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
SANCTUM_STATEFUL_DOMAINS="${SANCTUM_STATEFUL_DOMAINS:-praktikum-rpl-a-7-production.up.railway.app}"
EOF

echo "=== Running artisan commands ==="
php artisan config:clear || true
php artisan storage:link || true
php artisan migrate --force || true

# Berikan izin ke Apache (www-data) untuk menulis storage dan cache
chown -R www-data:www-data /app/storage /app/bootstrap/cache || true
chmod -R 775 /app/storage /app/bootstrap/cache || true

echo "=== Testing Apache syntax ==="
apache2ctl -t || true

echo "=== Starting Apache Web Server on port ${TARGET_PORT} ==="
exec apache2-foreground
