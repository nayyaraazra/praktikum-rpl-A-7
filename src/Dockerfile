FROM php:8.4-cli

# Install sistem, ekstensi PHP & Node.js
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Buat .env dummy agar artisan & vite build tidak error
RUN cp .env.example .env

# Install dependensi & build frontend
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install
RUN npm run build

# Bersihkan .env dummy (akan dibuat ulang saat runtime dari env vars Railway)
RUN rm .env

EXPOSE 8080

# Script startup: buat .env dari env vars Railway, lalu jalankan Laravel
CMD sh -c "\
  echo 'APP_NAME=${APP_NAME:-Laravel}' > .env && \
  echo 'APP_ENV=${APP_ENV:-production}' >> .env && \
  echo 'APP_KEY=${APP_KEY}' >> .env && \
  echo 'APP_DEBUG=${APP_DEBUG:-false}' >> .env && \
  echo 'APP_URL=${APP_URL}' >> .env && \
  echo 'DB_CONNECTION=${DB_CONNECTION:-mysql}' >> .env && \
  echo 'DB_HOST=${DB_HOST:-127.0.0.1}' >> .env && \
  echo 'DB_PORT=${DB_PORT:-3306}' >> .env && \
  echo 'DB_DATABASE=${DB_DATABASE:-laravel}' >> .env && \
  echo 'DB_USERNAME=${DB_USERNAME:-root}' >> .env && \
  echo 'DB_PASSWORD=${DB_PASSWORD}' >> .env && \
  echo 'SESSION_DRIVER=cookie' >> .env && \
  echo 'CACHE_STORE=file' >> .env && \
  echo 'QUEUE_CONNECTION=sync' >> .env && \
  echo 'FILESYSTEM_DISK=public' >> .env && \
  echo 'SANCTUM_STATEFUL_DOMAINS=${SANCTUM_STATEFUL_DOMAINS}' >> .env && \
  php artisan config:clear && \
  php artisan storage:link || true && \
  php artisan migrate --force || true && \
  exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"
