# syntax=docker/dockerfile:1

FROM php:8.2-fpm-alpine

# Install system deps
RUN apk add --no-cache \
    nginx \
    bash \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    sqlite-dev \
    zip unzip \
    git \
    nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql pdo_sqlite mbstring zip opcache gd \
    && docker-php-ext-enable opcache

# Nginx + PHP-FPM config
RUN rm -rf /var/www/html && mkdir -p /var/www/app
WORKDIR /var/www/app

# Copy Nginx config
COPY ./docker/nginx.conf /etc/nginx/nginx.conf

# Copy app code
COPY . /var/www/app

# Create .env from example so artisan commands work at build & runtime
RUN cp .env.example .env

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP deps (prod, no dev)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Generate a placeholder key (will be overwritten at runtime by start.sh)
RUN php artisan key:generate --force

# Build front-end assets
RUN npm install --no-fund --no-audit && npm run build

# Laravel permissions
RUN mkdir -p /var/www/app/storage/app/public \
             /var/www/app/storage/framework/cache \
             /var/www/app/storage/framework/sessions \
             /var/www/app/storage/framework/views \
             /var/www/app/storage/logs \
             /var/www/app/bootstrap/cache \
    && chown -R www-data:www-data /var/www/app/storage /var/www/app/bootstrap/cache \
    && chown www-data:www-data /var/www/app/.env

# Copy startup script
COPY ./docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
