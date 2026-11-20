FROM php:8.4-apache

# Install dependencies sistem, ekstensi PHP & Node.js
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP untuk Laravel
RUN docker-php-ext-install pdo_mysql mbstring pcntl bcmath gd zip

# Pastikan HANYA mpm_prefork yang aktif (matikan mpm_event & mpm_worker penyebab AH00534)
RUN a2dismod mpm_event || true \
    && a2dismod mpm_worker || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Konfigurasi VirtualHost Apache secara statis dan pasti
RUN printf '<VirtualHost *>\n\
    DocumentRoot /app/public\n\
    <Directory /app/public>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog /dev/stderr\n\
    TransferLog /dev/stdout\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

# Global ServerName agar tidak ada warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# .env dummy untuk build aset Vite & artisan
RUN cp .env.example .env

# Install dependensi PHP & build frontend Vue (Vite)
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install
RUN npm run build
RUN rm .env

# Permission script startup
RUN chmod +x start.sh

EXPOSE 8080

CMD ["sh", "start.sh"]
