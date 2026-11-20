FROM php:8.4-apache

# Install dependencies sistem, ekstensi PHP & Node.js
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP untuk Laravel
RUN docker-php-ext-install pdo_mysql mbstring pcntl bcmath gd zip

# Enable mod_rewrite Apache untuk routing Laravel
RUN a2enmod rewrite

# Arahkan DocumentRoot Apache ke /app/public
ENV APACHE_DOCUMENT_ROOT=/app/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# .env dummy untuk build aset Vite & artisan
RUN cp .env.example .env

# Install dependensi & build frontend Vue (Vite)
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install
RUN npm run build
RUN rm .env

# Permission script startup
RUN chmod +x start.sh

EXPOSE 8080

CMD ["sh", "start.sh"]
