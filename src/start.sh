#!/bin/sh

echo "=== KULAAN.id Startup ==="

export PORT=${PORT:-8080}

# Update ports.conf agar Apache hanya mendengarkan port $PORT
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# Masukkan PORT dan ServerName ke konfigurasi Apache
echo "export PORT=${PORT}" >> /etc/apache2/envvars
grep -q "ServerName localhost" /etc/apache2/apache2.conf || echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Buat .env dari environment variables Railway
cat > .env <<EOF
APP_NAME=${APP_NAME:-KULAAN.id}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-true}
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

echo "=== Testing Apache config ==="
apache2ctl configtest || true

echo "=== Starting Apache Web Server on port ${PORT} ==="
exec apache2-foreground
