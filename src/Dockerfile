FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# .env dummy untuk build (artisan & vite butuh ini)
RUN cp .env.example .env

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install
RUN npm run build

# Hapus .env dummy, akan dibikin ulang oleh start.sh saat runtime
RUN rm .env

# Copy startup script
RUN chmod +x start.sh

EXPOSE 8080

CMD ["sh", "start.sh"]
