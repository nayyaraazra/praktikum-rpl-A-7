FROM php:8.4-cli

# Install dependencies sistem, ekstensi PHP & Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP yang dibutuhkan Laravel
RUN docker-php-ext-install pdo_mysql mbstring pcntl bcmath gd zip

# Ambil Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy seluruh source code
COPY . .

# Install PHP dependencies & build frontend Vue (Vite)
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install
RUN npm run build

# Port default Railway
EXPOSE 8080

# Jalankan storage:link, migrasi database di background agar tidak memblokir server, dan langsung start web server
CMD sh -c "php artisan storage:link || true; (php artisan migrate --force &); exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"
