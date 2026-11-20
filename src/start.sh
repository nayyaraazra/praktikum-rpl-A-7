#!/bin/sh
set -e

echo "=== KULAAN.id Startup ==="

# Set Apache listening port sesuai environment variable PORT dari Railway
TARGET_PORT=${PORT:-8080}
sed -i "s/Listen 80/Listen ${TARGET_PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${TARGET_PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Buat .env dari environment variables Railway
cat > .env <<EOF
APP_NAME=${APP_NAME:-KULAAN.id}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
ASSET_URL=${APP_URL:-http://localhost}
DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-railway}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}
SESSION_DRIVER=cookie
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
SANCTUM_STATEFUL_DOMAINS=${SANCTUM_STATEFUL_DOMAINS:-localhost}
EOF

echo "=== Running artisan commands ==="
php artisan config:clear || true
php artisan storage:link 2>/dev/null || true
php artisan migrate --force 2>/dev/null || true

# Berikan izin ke Apache (www-data) untuk menulis storage dan cache
chown -R www-data:www-data /app/storage /app/bootstrap/cache || true
chmod -R 775 /app/storage /app/bootstrap/cache || true

echo "=== Starting Apache Web Server on port ${TARGET_PORT} ==="
exec apache2-foreground
